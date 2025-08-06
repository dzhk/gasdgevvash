<?php

namespace app\controllers;

use app\models\Post;
use app\services\verification\HumanVerificationInterface;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\services\PostService;
use app\services\MailService;

class PostController extends Controller
{
    private PostService $postService;
    private MailService $mailService;
    private HumanVerificationInterface $humanVerification;

    public function __construct(
        $id,
        $module,
        PostService $postService,
        MailService $mailService,
        HumanVerificationInterface $humanVerification,
        $config = []
    ) {
        $this->postService = $postService;
        $this->mailService = $mailService;
        $this->humanVerification = $humanVerification;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'edit', 'delete', 'confirm-delete'],
                'rules' => [
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['edit', 'delete', 'confirm-delete'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                        'matchCallback' => function ($rule, $action) {
                            $token = Yii::$app->request->get('token');
                            $postId = Yii::$app->request->get('id');
                            try {
                                $this->postService->getPost($postId, $token);
                                return true;
                            } catch (\Exception $e) {
                            return false;
                            }
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $posts = $this->postService->getPosts();
        $model = new Post();
        return $this->render('index', ['posts' => $posts, 'model' => $model]);
    }

    public function actionCreate()
    {
        $ip = Yii::$app->request->userIP;

        if (!$this->postService->canPostAgain($ip)) {
            $cooldownLeft = $this->postService->getCooldownTimeLeft($ip);
            $minutes = ceil($cooldownLeft / 60);

            Yii::$app->session->setFlash('error',
                "Вы можете отправить следующее сообщение через $minutes минут(ы).");
            return $this->redirect(['index']);
        }

        if (!$this->humanVerification->verify(Yii::$app->request->post())) {
            Yii::debug(Yii::$app->request->post(), 'recaptcha');
            Yii::$app->session->setFlash('error', 'Вам нужно подтвердить, что вы человек.');
            return $this->redirect(['index']);
        }

        try {
            $post = $this->postService->createPost(Yii::$app->request->post(), $ip);
            $this->mailService->sendPostNotification($post);
                Yii::$app->session->setFlash('success', 'Сообщение успешно опубликовано!');
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    public function actionEdit($id, $token)
    {
        try {
            $post = $this->postService->getPost($id, $token);

            if (Yii::$app->request->isPost) {
                $post = $this->postService->editPost($id, $token, Yii::$app->request->post('message'));
            Yii::$app->session->setFlash('success', 'Сообщение успешно обновлено!');
            return $this->redirect(['index']);
        }

            return $this->render('edit', ['model' => $post]);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['index']);
        }
    }

    public function actionDelete($id, $token)
    {
        try {
            $this->postService->deletePost($id, $token);
            Yii::$app->session->setFlash('success', 'Сообщение успешно удалено!');
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

            return $this->redirect(['index']);
        }

    public function actionConfirmDelete($id, $token)
    {
        try {
            $post = $this->postService->getPost($id, $token);
            return $this->render('confirm-delete', ['model' => $post]);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect(['index']);
            }
        }
}