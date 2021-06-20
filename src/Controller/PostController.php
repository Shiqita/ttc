<?php


namespace App\Controller;


use App\Entity\Post;
use App\Form\PostFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{

    #[Route('/post_create', name: 'post_create')]
    public function createPost(Request $request) {
        $post = new Post();
        $postForm = $this->createForm(PostFormType::class, $post);
        $postForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid()) {
            $post->setTitle($postForm->get('title')->getData());
            $post->setContent($postForm->get('content')->getData());
            $post->setCategory($postForm->get('category')->getData());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('post_create');
        }
        return $this->render('create_post.html.twig', [
            'postForm' => $postForm->createView()
        ]);
    }
}