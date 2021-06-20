<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentFormType;
use App\Form\PostFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index() {
        $entityManager = $this->getDoctrine()->getManager();
        return $this->render('home/index.html.twig', [
            'posts' => $entityManager->getRepository(Post::class)->getPosts()
        ]);
    }
    #[Route('/post/{post_id}', name: 'full_post')]
    public function fullPost(Request $request) {
        $post_id = $request->attributes->get('post_id');
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Post::class)->find($post_id);
        $comment = new Comment();
        $commentForm = $this->createForm(CommentFormType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setContent($commentForm->get('content')->getData());
            $comment->setPost($post);
            $comment->setAuthor($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('full_post', ['post_id' => $post_id]);
        }
        return $this->render('full_post.html.twig', [
            'post' => $post,
            'comments' => $post->getComments([
                'createdAt' => 'ASC'
            ]),
            'commentForm' => $commentForm->createView()
        ]);
    }
    #[Route('/category/{category_id}', name: 'category')]
    public function categoryPosts($category_id) {
        $entityManager = $this->getDoctrine()->getManager();
        $category = $entityManager->getRepository(Category::class)->find($category_id);
        return $this->render('category.html.twig', [
            'posts' => $category->getPosts([
                'createdAt' => 'DESC'
            ]),
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
    #[Route('/{post_id}/create_comment', name: 'create_comment')]
    public function createComment(Request $request) {
        $comment = new Comment();
        $commentForm = $this->createForm(CommentFormType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setContent($commentForm->get('content')->getData());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('full_post.html.twig', [
            'postForm' => $postForm->createView()
        ]);
    }
}