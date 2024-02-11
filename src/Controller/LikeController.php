<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Repository\MicroPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LikeController extends AbstractController
{
    #[Route('/like/{id}', name: 'app_like')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function like(MicroPost $post, MicroPostRepository $microPostRepository, Request $request): Response
    {
        $post->addLikedBy($this->getUser());
        $microPostRepository->add($post, true);
        $this->addFlash('success', 'Post successfully liked!');
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/unlike/{id}', name: 'app_unlike')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function unlike(MicroPost $post, MicroPostRepository $microPostRepository, Request $request): Response
    {
        $post->removeLikedBy($this->getUser());
        $microPostRepository->add($post, true);
        $this->addFlash('success', 'Post successfully unliked!');
        return $this->redirect($request->headers->get('referer'));
    }
}
