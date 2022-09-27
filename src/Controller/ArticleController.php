<?php

namespace App\Controller;

use App\Entity\Article;
use App\Handler\ArticleHandler;
use App\Handler\ResponseHandler;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{

    private $responseHandler;
    private $articleHandler;

    public function __construct(ResponseHandler $responseHandler, ArticleHandler $articleHandler)
    {
        $this->responseHandler  = $responseHandler;
        $this->articleHandler   = $articleHandler;
    }

    /**
     * @Route("/create", name="create_article", methods={"POST"})
     */
    public function create(ManagerRegistry $manager, Request $request)
    {
        $em     = $manager->getManager();
        $data   = $request->request->all();

        $image  = $request->files->get('image');

        try {
            $this->articleHandler->validateParams($data, $image);
            $article = $this->articleHandler->setArticle($em, $data, $image);
        } catch (\Exception $e) {
            $response = $this->responseHandler->errorResponse($e->getMessage());
            return $response;
        }

        $message = 'Article has been created successfully';
        return $this->responseHandler->successResponse($article, $message);
    }

    /**
     * @Route("/get_all", name="get_all_articles", methods={"GET"})
     */
    public function getAll(ManagerRegistry $manager, PaginatorInterface $paginator, Request $request)
    {
        $em     = $manager->getManager();
        //Tendria que traerlas paginadas
        $query  = $em->getRepository(Article::class)->getAllArticles();

        try {
            $page               = $request->query->getInt('page', 1);

            $items_per_page     = 10;
    
            $pagination         = $paginator->paginate($query, $page, $items_per_page);
            $total_items_count  = $pagination->getTotalItemCount();

            $data               = array(
                'total_items_count'         => $total_items_count,
                'actual_page'               => $page,
                'items_per_page'            => $items_per_page,
                //ceil para redondear. divide el total de elementos por el total por pagina
                'total_pages'               => ceil($total_items_count / $items_per_page),
                'data'                      => $pagination
            );
        } catch (\Exception $e) {
            $response = $this->responseHandler->errorResponse($e->getMessage());
            return $response;
        }

        return $this->responseHandler->successResponse($data, "Ok");
    }

    /**
     * @Route("/get_one/{id}", name="get_one_article", methods={"GET"})
     */
    public function getOne(ManagerRegistry $manager, $id = null)
    {
        $em         = $manager->getManager();
        $article    = $this->articleHandler->findArticleById($em, $id);
        if (is_null($article)) {
            return $this->responseHandler->errorResponse("Article not found");
        }

        return $this->responseHandler->successResponse($article);
    }

    /**
     * @Route("/edit/{id}", name="edit_article", methods={"POST"})
     */
    public function edit(ManagerRegistry $manager, Request $request, $id = null)
    {
        $em         = $manager->getManager();

        $article    = $this->articleHandler->findArticleById($em, $id);
        if (is_null($article)) {
            return $this->responseHandler->errorResponse("Article not found");
        }

        $previousImage  = $article->getImage();
        //Esta es la imagen que tenia el articulo antes de ser editado para borrarla de la carpeta uploads
        $data           = $request->request->all();
        $image          = $request->files->get('image');

        try {
            $this->articleHandler->validateParams($data, $image);
            $article = $this->articleHandler->setArticle($em, $data, $image, $article);
            //No lo puedo poner antes porque si falla el SETTER, borró la imagen y en la próxima pasada va a tirar error pq la imagen ya no existe
            unlink('uploads/articles/' . $previousImage);
        } catch (\Exception $e) {
            $response = $this->responseHandler->errorResponse($e->getMessage());
            return $response;
        }
        //Si llegó hasta acá quiere decir que no hubo werrores, acá podria borrar la foto anterior
        $message = 'Article has been edited successfully';
        return $this->responseHandler->successResponse($article, $message);
    }

    /**
     * @Route("/delete/{id}", name="delete_article", methods={"POST"})
     */
    public function delete(ManagerRegistry $manager, Request $request, $id = null)
    {
        $em         = $manager->getManager();

        $article    = $this->articleHandler->findArticleById($em, $id);
        if (is_null($article)) {
            return $this->responseHandler->errorResponse("Article not found");
        }

        //Obtengo la imagen antes de borrar el articulo para borrarla
        $previousImage  = $article->getImage();

        try {
            $em->remove($article);
            $em->flush();
            unlink('uploads/articles/' . $previousImage);
        } catch (\Exception $e) {
            $response = $this->responseHandler->errorResponse($e->getMessage());
            return $response;
        }

        $message = 'Article has been deleted successfully';
        return $this->responseHandler->successResponse($article, $message);
    }
}
