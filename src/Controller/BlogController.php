<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Post;
use App\Form\PostFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index() {
        $entityManager = $this->getDoctrine()->getManager();
        $posts = $entityManager->getRepository(Post::class)->findAll();
        return $this->render('home/index.html.twig', [
            'posts' => $posts
        ]);
    }
    #[Route('/category/{category_id}', name: 'category')]
    public function category_posts($category_id) {
        $entityManager = $this->getDoctrine()->getManager();
        $category = $entityManager->getRepository(Category::class)->find($category_id);
        return $this->render('category.html.twig', [
            'posts' => $category->getPosts(),
            'categoryName' => $category->getName()
        ]);
    }
    #[Route('/create_post', name: 'create_post')]
    public function createPost(Request $request) {
        $post = new Post();
        $postForm = $this->createForm(PostFormType::class, $post);
        $postForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid()) {
            $post->setTitle($postForm->get('title')->getData());
            $post->setContent($postForm->get('content')->getData());
            $post->setCategory($postForm->get('category')->getData());
            $post->setAuthor($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('create_post.html.twig', [
            'postForm' => $postForm->createView()
        ]);
    }

}