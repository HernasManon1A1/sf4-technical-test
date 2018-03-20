<?php

namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{

    /**
     * @var \GuzzleHttp\Client $client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://api.github.com','verify' => false]);
    }

    /**
     * @Route("/", name="home")
     * @IsGranted("ROLE_USER")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('query', TextType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $query = $form->getData()['query'];
            if (isset($query) && !is_null($query)) {
                $response = $this->client->request('GET', '/search/users', ['query' => ['q' => $query]]);
                if ($response->getStatusCode() == "200") {
                    $body = json_decode($response->getBody()->getContents());
                    if ($body->total_count > 0 ) {
                        exit(var_dump($body));
                    } else {
                        $this->addFlash("error", "Utilisateur non trouvé");
                    }
                } else {
                    $this->addFlash("error", "GIT HS? ¯\_(ツ)_/¯");
                }
            }
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form->createView()
        ]);
    }
}
