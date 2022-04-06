<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\StateMachine;
use Symfony\Component\Workflow\WorkflowInterface;

class PostController extends AbstractController
{
    /**
     * @var StateMachine
     */
    private $stateMachine;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(WorkflowInterface $postStateStateMachine, EntityManagerInterface $entityManager)
    {
        $this->stateMachine = $postStateStateMachine;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/post/{id}", name="app_post")
     */
    public function index(Post $post, Request $request): Response
    {
        if ($request->query->has('apply')) {
            if (!$this->stateMachine->can($post, $request->query->get('apply'))) {
                $this->addFlash('success', 'tesssst');
                $this->redirectToRoute('app_post', ['id' => $post->getId()]);
            }
            $this->stateMachine->apply($post, $request->query->get('apply'));
            $this->entityManager->persist($post);
            $this->entityManager->flush();
        }
        return $this->render('post/index.html.twig', [
            'post' => $post,
        ]);
    }
}
