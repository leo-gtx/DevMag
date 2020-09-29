<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Articles;
use App\Entity\Categories;
use App\Entity\Commentaires;
use App\Form\AjoutArticleType;
use App\Form\CommentairesType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request; // Nous avons besoin d'accéder à la requête pour obtenir le numéro de page
use Knp\Component\Pager\PaginatorInterface; // Nous appelons le bundle KNP Paginator
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ArticlesController
 * @package App\Controller
 * @Route("/articles", name="actualites_")
 */
class ArticlesController extends AbstractController
{
    
    /**
     * @Route("/", name="articles")
     */
    public function index(Request $request, PaginatorInterface $paginator) // Nous ajoutons les paramètres requis
    {
        // Méthode findBy qui permet de récupérer les données avec des critères de filtre et de tri
        $donnees = $this->getDoctrine()->getRepository(Articles::class)->findBy(['published' => true],['created_at' => 'desc']);

        $articles = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );
        
        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/{slug}", name="article")
    */
    public function article($slug, Request $request){
        // On récupère l'article correspondant au slug
        $article = $this->getDoctrine()->getRepository(Articles::class)->findOneBy(['slug' => $slug]);
        
        
        $commentaires = $this->getDoctrine()->getRepository(Commentaires::class)->findBy([
            'articles' => $article,
            'actif' => 0
        ],['created_at' => 'desc']);

        if(!$article){
            // Si aucun article n'est trouvé, nous créons une exception
            throw $this->createNotFoundException('L\'article n\'existe pas');
        }

        // Nous créons l'instance de "Commentaires"
        $commentaire = new Commentaires();

        // Nous créons le formulaire en utilisant "CommentairesType" et on lui passe l'instance
        $form = $this->createForm(CommentairesType::class, $commentaire);

        // Nous récupérons les données
        $form->handleRequest($request);

        // Nous vérifions si le formulaire a été soumis et si les données sont valides
        if ($form->isSubmitted() && $form->isValid()) {
            // Hydrate notre commentaire avec l'article
            $commentaire->setArticles($article);

            // Hydrate notre commentaire avec la date et l'heure courants
            $commentaire->setCreatedAt(new \DateTime('now'));

            $doctrine = $this->getDoctrine()->getManager();

            // On hydrate notre instance $commentaire
            $doctrine->persist($commentaire);

            // On écrit en base de données
            $doctrine->flush();
        }
        $doctrine = $this->getDoctrine()->getManager();
        //On maj le nombre de vue
        $article->setView($article->getView() + 1);
        // On hydrate notre instance $commentaire
        $doctrine->persist($article);

        // On écrit en base de données
        $doctrine->flush();

        //On recupere des Articles relatifs
        $relatedArticles = new ArrayCollection();
        foreach ($article->getCategories() as $category) {
            if($relatedArticles->count() > 2) break;
            $items = $this->getDoctrine()->getRepository(Articles::class)->findByCategory($category->getSlug(),2);
            foreach($items as $item){
                if($article->getSlug() !== $item->getSlug()){
                    if(!$relatedArticles->contains($item)){
                        $relatedArticles[] = $item;
                    } 
            }
        }
    }

    if($article->getVip() === true){
        $user = $this->getUser();
        if($user){
        if(
        $user->getMembershipContract() >= new \DateTime() ||
         in_array('ROLE_ADMIN',$user->getRoles()) ||
          $article->getUsers() === $user ){
                    // Si l'article existe nous envoyons les données à la vue
                return $this->render('articles/article.html.twig', [
                    'form' => $form->createView(),
                    'article' => $article,
                    'relatedArticles' => $relatedArticles,
                    'commentaires' => $commentaires
                ]);
        }
        }
        throw new \Exception("You cannot access to this page", 1);  
        
    }else{
         // Si l'article existe nous envoyons les données à la vue
        return $this->render('articles/article.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'commentaires' => $commentaires
        ]);
    }
       
    }

    /**
     * @Route("/categorie/{slug}", name="articles_by_category")
     */
    public function articlesByCategory($slug,Request $request, PaginatorInterface $paginator){
        // Méthode findBy qui permet de récupérer les données avec des critères de filtre et de tri
       // $category = $this->getDoctrine()->getRepository(Categories::class)->find($id);
        $donnees = $this->getDoctrine()->getRepository(Articles::class)->findByCategory($slug);

        $articles = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );
        
        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

     /**
     * @Route("/tags/{slug}", name="articles_by_keyword")
     */
    public function articlesByKeyword($slug,Request $request, PaginatorInterface $paginator){
        // Méthode findBy qui permet de récupérer les données avec des critères de filtre et de tri
       // $category = $this->getDoctrine()->getRepository(Categories::class)->find($id);
        $donnees = $this->getDoctrine()->getRepository(Articles::class)->findByKeyword($slug);

        $articles = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );
        
        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

     /**
     * @Route("/bookmark/{slug}", name="article_add_bookmark")
     */
    public function addBookmark($slug,Request $request){
        // Méthode findBy qui permet de récupérer les données avec des critères de filtre et de tri
       // On récupère l'article correspondant au slug
       $article = $this->getDoctrine()->getRepository(Articles::class)->findOneBy(['slug' => $slug]);
       $user = $this->getUser();
       $flag = false;
       foreach ($user->getBookmarks() as $bookmark) {
            if($bookmark->getId() === $article->getId()){
               $flag = true;
            break;
            }
        
       } 
       if(!$flag){
        $user->addBookmark($article);
            $doctrine = $this->getDoctrine()->getManager();

           // On hydrate notre instance $commentaire
           $doctrine->persist($user);

           // On écrit en base de données
           $doctrine->flush();

           //Send message
           $this->addFlash('message', 'The article was added to your bookmarks');
        }else{
            $user->removeBookmark($article);

            $doctrine = $this->getDoctrine()->getManager();

            // On hydrate notre instance $commentaire
            $doctrine->persist($user);
 
            // On écrit en base de données
            $doctrine->flush();
            //Send message
            $this->addFlash('message', 'The article was removed to your bookmarks');
        }
      
       

        //return $this->redirect(str_replace('/bookmark','',$request->getUri()));
        return $this->redirect($request->headers->get('referer'));

        
       
    }

    /**
     * @Route("/like/{slug}", name="article_like")
     */
    public function like($slug,Request $request){
       
        // On récupère l'article correspondant au slug
        $article = $this->getDoctrine()->getRepository(Articles::class)->findOneBy(['slug' => $slug]);
        $article->setLikes($article->getLikes() + 1);
        $doctrine = $this->getDoctrine()->getManager();

        // On hydrate notre instance $commentaire
        $doctrine->persist($article);

        // On écrit en base de données
        $doctrine->flush();
        
        return $this->redirect($request->headers->get('referer'));
    }

   

    

    /**
     * @IsGranted("ROLE_EDITOR")
     * @Route("/article/ajouter", name="ajout_article")
     */
    public function ajout(Request $request,TranslatorInterface $translator)
    {
        $article = new Articles();

        $form = $this->createForm(AjoutArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUsers($this->getUser());

			$image = $form->get('featured_image')->getData();
			if($image){
				$fichier = md5(uniqid()).'.'.$image->guessExtension();
				$image->move(
					$this->get('kernel')->getProjectDir().'/public/uploads/images/featured',
					$fichier
				);
				$article->setFeaturedImage($fichier);
			}
            $article->setView(0);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            $message = $translator->trans('Article published successfully');

            $this->addFlash('message', $message);
            return $this->redirectToRoute('actualites_articles');
        }


        
        return $this->render('articles/ajout.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }


    /**
     * @IsGranted("ROLE_EDITOR")
     * @Route("/article/editer/{slug}", name="edition_article")
     */
    public function edition($slug, Request $request,TranslatorInterface $translator)
    {
        $article = $this->getDoctrine()->getRepository(Articles::class)->findOneBy(['slug' => $slug]);

        $form = $this->createForm(AjoutArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUsers($this->getUser());

			$image = $form->get('featured_image')->getData();
			if($image){
				$fichier = md5(uniqid()).'.'.$image->guessExtension();
				$image->move(
					$this->getParameter('app.path.featured_images'),
					$fichier
				);
				$article->setFeaturedImage($fichier);
			}

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            //$message = $translator->trans('Article published successfully');

            $this->addFlash('message', 'Article updated!');
            return $this->redirectToRoute('actualites_ajout_article');
        }


        
        return $this->render('articles/ajout.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/tutorials/preview", name="tutorials")
     */
    public function preview(Request $request, PaginatorInterface $paginator){
         // Méthode findBy qui permet de récupérer les données avec des critères de filtre et de tri
         $donnees = $this->getDoctrine()->getRepository(Articles::class)->findBy(['published' => true, 'isFormation' => true],['created_at' => 'desc']);

         $articles = $paginator->paginate(
             $donnees, // Requête contenant les données à paginer (ici nos articles)
             $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
             5 // Nombre de résultats par page
         );

        return $this->render('articles/formations.html.twig',[
            'articles' => $articles
        ]);
    }
}


