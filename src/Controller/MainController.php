<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Articles;
use App\Entity\Categories;
use App\Entity\MotsCles;
use Knp\Component\Pager\PaginatorInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
         // Méthode findBy qui permet de récupérer les données avec des critères de filtre et de tri
         $donnees = $this->getDoctrine()->getRepository(Articles::class)->findBy(['published' => true],['created_at' => 'desc']);
         $tendances = $this->getDoctrine()->getRepository(Articles::class)->findBy(['published' => true],['updated_at' => 'desc'],4);

         $articles = $paginator->paginate(
             $donnees, // Requête contenant les données à paginer (ici nos articles)
             $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
             6 // Nombre de résultats par page
         );

        // Nous générons la vue de la page d'accueil
        return $this->render('main/index.html.twig', [
            'articles' => $articles,
            'tendances' => $tendances
        ]);
    }

    /**
     * @Route("/mentions-legales", name="mentions-legales")
     */
    public function mentionsLegales()
    {
        // Nous générons la vue de la page des mentions légales
        return $this->render('main/mentions-legales.html.twig');
    }
    /**
     * @Route("/change_locale/{locale}", name="change_locale")
     */
    public function changeLocale($locale, Request $request)
    {
        //$request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/header", name="header")
     */
    public function header()
    {
        //On ercupere toutes les categories et les mots cles
        $categories = $this->getDoctrine()->getRepository(Categories::class)->findAll();
        $motscles = $this->getDoctrine()->getRepository(MotsCles::class)->findAll();

        return $this->render('main/header.html.twig',[
            'categories'=>$categories,
            'motscles' => $motscles
        ]);

    }

    /**
     * @Route("/sidenav", name="sidenav")
     */
    public function sidenav(){
        $recents = $this->getDoctrine()->getRepository(Articles::class)->findBy(['published' => true],['created_at' => 'desc'],3);
        $populars = $this->getDoctrine()->getRepository(Articles::class)->findBy(['published' => true],['view' => 'desc'],3);
        return $this->render('main/sidenav.html.twig',[
            'recents'=>$recents,
            'populars' => $populars
        ]);

    }

      /**
     * @Route("/privacy", name="privacy")
     */
    public function privacy()
    {
        // Nous générons la vue de la page des mentions légales
        return $this->render('FAQ/privacy.html.twig');
    }

     /**
     * @Route("/termsandconditions", name="terms_and_conditions")
     */
    public function termsAndConditions()
    {
        // Nous générons la vue de la page des mentions légales
        return $this->render('FAQ/termsAndConditions.html.twig');
    }

}
