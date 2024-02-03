<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use App\Security\Voter\MicroPostVoter;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(MicroPostRepository $microPostRepository): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $microPostRepository->findAllWithComments()
        ]);
    }

    #[Route('/add-post', name: 'app_micro_post_add')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(Request $request, MicroPostRepository $microPostRepository): Response
    {
        /*$this->denyAccessUnlessGranted(
            'IS_AUTHENTICATED_FULLY'
        );*/

        $form = $this->createForm(MicroPostType::class, new MicroPost());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setCreated(new DateTime());
            $post->setAuthor($this->getUser());

            $microPostRepository->add($post, true);

            $this->addFlash('success', 'Your post has been successfully added!');
            return $this->redirectToRoute('app_micro_post');
        }

        return $this->render(
            '/micro_post/new.html.twig',
            [
                'form' => $form
            ]
        );
    }

    #[Route('/show-post/{id}', name: 'app_micro_post_show')]
    #[IsGranted(MicroPostVoter::VIEW, 'post')]
    public function show(MicroPost $post): Response
    {
        return $this->render('micro_post/show.html.twig', [
            'post' => $post
        ]);
    }

    #[Route('/edit-post/{id}', name: 'app_micro_post_edit')]
    #[IsGranted(MicroPostVoter::EDIT, 'post')]
    public function edit(MicroPost $post, Request $request, MicroPostRepository $microPostRepository): Response
    {
        $form = $this->createForm(MicroPostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setCreated(new DateTime());

            $microPostRepository->add($post, true);

            $this->addFlash('success', 'Your post has been successfully edited!');
            return $this->redirectToRoute('app_micro_post_show', ['id' => $post->getId()]);
        }

        return $this->render(
            '/micro_post/edit.html.twig',
            [
                'post' => $post,
                'form' => $form
            ]
        );
    }

    #[Route('/edit-post/{id}/comment', name: 'app_micro_post_add_comment')]
    #[IsGranted('ROLE_COMMENTOR')]
    public function addComment(MicroPost $post, Request $request, CommentRepository $commentRepository): Response
    {
        $form = $this->createForm(CommentType::class, new Comment());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setMicroPost($post);
            $post->setCreated(new DateTime());
            $comment->setAuthor($this->getUser());

            $commentRepository->add($comment, true);

            $this->addFlash('success', 'Your comment has been successfully added!');
            return $this->redirectToRoute('app_micro_post_show', ['id' => $post->getId()]);
        }

        return $this->render(
            '/micro_post/comment.html.twig',
            [
                'form' => $form,
                'post' => $post
            ]
        );
    }
}
