<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Service\ApiCaller;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Builder\Class_;
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

                $limit = 0;
                foreach ($users as $key => $user) {
                    // Nombres de requêtes limité, on limite les appels
                    if ($limit >= 5) {
                        $users[$key]->name = 'Hernas Manon';
                        $users[$key]->location = "Roubaix";
                        $users[$key]->email = "hernas.manon@gmail.com";
                        $users[$key]->bio = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean interdum non lacus at sagittis. Integer a molestie sem. Aliquam fringilla convallis sem, sit amet ultrices eros pellentesque eget.";
                        $users[$key]->public_repos = 6;
                    } else {
                        try {
                            $infos = $apiCaller->call('/users/'.$user->login);
                            $users[$key]->name = $infos->name;
                            $users[$key]->location = $infos->location;
                            $users[$key]->email = $infos->email;
                            $users[$key]->bio = $infos->bio;
                            $users[$key]->public_repos = $infos->public_repos;
                        } catch (\Exception $e) {
                            $users[$key]->name = 'Hernas Manon';
                            $users[$key]->location = "Roubaix";
                            $users[$key]->email = "hernas.manon@gmail.com";
                            $users[$key]->bio = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean interdum non lacus at sagittis. Integer a molestie sem. Aliquam fringilla convallis sem, sit amet ultrices eros pellentesque eget.";
                            $users[$key]->public_repos = 6;
                        }
                    }
                    $limit++;
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
        $repositories = array();
        $choices = array();
        try {
            // Nombre de requêtes limité
            $repositories = $apiCaller->call('users/'.$username.'/repos');
            foreach ($repositories as $repository) {
                $choices[$repository->full_name] = $repository->full_name;
            }
        } catch (\Exception $e) {
            $repository = new \stdClass();
            $repository->full_name = 'stadline/sf2-technical-test';
            $repository->description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean interdum non lacus at sagittis. Integer a molestie sem.';
            $repository->html_url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
            $repositories[] = $repository;
            $repositories[] = $repository;

            $choices = array(
                'comp1/repo1' => 'comp1/repo1',
                'comp2/repo2' => 'comp2/repo2',
                'comp3/repo3' => 'comp3/repo3',
            );
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment, array('repositories' => $choices));

        $oldCommentaries = $entityManager->getRepository(Comment::class)->findBy(array('owner' => $username));
        $form->handleRequest($request);
        if (
            $form->isSubmitted() &&
            $form->isValid()
        ) {
            $comment->setAuthor($this->getUser());
            $comment->setOwner($username);
            $entityManager->persist($comment);
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Le commentaire a été ajouté avec succès');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'ajout du commentaire');
            }
            return $this->redirectToRoute('comment', array('username' => $username));
        }

        return $this->render('home/comment.html.twig', [
            'form' => $form->createView(),
            'old_commentaries' => $oldCommentaries,
            'username' => $username,
            'repositories' => $repositories
        ]);
    }
}
