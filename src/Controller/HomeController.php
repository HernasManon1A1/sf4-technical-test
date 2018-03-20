<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Service\ApiCaller;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{

    /**
     * @Route("/", name="home")
     * @IsGranted("ROLE_USER")
     *
     * @param Request $request
     * @param ApiCaller $apiCaller
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction(Request $request, ApiCaller $apiCaller)
    {
        $users = null;
        $form = $this->createFormBuilder()
            ->add('query', TextType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $query = $form->getData()['query'];
            if (isset($query) && !is_null($query)) {
                $users = $apiCaller->call('/search/users', array('q' => $query));
                if (!$users) {
                    $this->addFlash('error', 'Utilisateur non trouvé');
                }
            }
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'users' => $users
        ]);
    }

    /**
     * @Route("/{username}/comment", name="comment")
     * @IsGranted("ROLE_USER")
     *
     * @param string $username
     * @param Request $request
     * @param ApiCaller $apiCaller
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function commentAction(string $username, Request $request, ApiCaller $apiCaller, EntityManagerInterface $entityManager)
    {
        $response = $apiCaller->call('users/'.$username.'/repos');
        $choices = array();
        foreach ($response as $repository) {
            $choices[$repository->full_name] = $repository->full_name;
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment, array('repositories' => $choices));

        $oldCommentaries = $entityManager->getRepository(Comment::class)->findBy(array('author' => $this->getUser()));
        $form->handleRequest($request);
        if (
            $form->isSubmitted() &&
            $form->isValid()
        ) {
            $comment->setAuthor($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        return $this->render('home/comment.html.twig', [
            'form' => $form->createView(),
            'old_commentaries' => $oldCommentaries
        ]);
    }
}
