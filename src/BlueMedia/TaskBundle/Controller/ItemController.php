<?php

namespace BlueMedia\TaskBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use BlueMedia\TaskBundle\Entity\Item;
use BlueMedia\TaskBundle\Form\ItemType;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\QueryBuilder;

/**
 * Item controller.
 *
 */
class ItemController extends FOSRestController {

    /**
     *
     * @return json
     */
    public function getItemsAction(Request $request) {
        $instock = $request->query->get('inStock');
        $minStock = $request->query->get('minStock');
        if ($instock == 'true') {
            $items = $this->getDoctrine()->getRepository("BlueMediaTaskBundle:Item")->createQueryBuilder('i')
                            ->where('i.amount > :amount')
                            ->setParameter('amount', '0')
                            ->getQuery()->getResult();
        } else if ($instock == 'false') {
            $items = $this->getDoctrine()->getRepository("BlueMediaTaskBundle:Item")->findBy(array(
                'amount' => '0'
            ));
        } else if (isset($minStock) && $minStock != NULL) {
            $items = $this->getDoctrine()->getRepository("BlueMediaTaskBundle:Item")->createQueryBuilder('i')
                            ->where('i.amount > :amount')
                            ->setParameter('amount', $minStock)
                            ->getQuery()->getResult();
        } else {
            $items = $this->getDoctrine()->getRepository("BlueMediaTaskBundle:Item")->findAll();
        }
        $view = $this->view($items, 200)
                ->setFormat('json');

        return $this->handleView($view);
    }

    /**
     *
     * @return json
     */
    public function getItemAction($id, Request $request) {
        $item = $this->getDoctrine()->getRepository("BlueMediaTaskBundle:Item")->find($id);

        if (!$item) {
            $view = $this->view(array(
                        'error_msg' => 'no item found',
                        'result' => 'failure'
                            ), 404)
                    ->setFormat('json');

            return $this->handleView($view);
        }

        $view = $this->view($item, 200)
                ->setFormat('json');

        return $this->handleView($view);
    }

    /**
     * Creates a new Item entity.
     *
     */
    public function createAction(Request $request) {
        $name = $request->query->get('name');
        $stock = $request->query->get('stock');

        if (!$name) {
            $view = $this->view(array(
                        'error_msg' => 'name required',
                        'result' => 'failure'
                            ), 404)
                    ->setFormat('json');

            return $this->handleView($view);
        }

        $item = new Item();

        $item->setName(trim($name));
        $item->setAmount((int) $stock);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($item);
        $em->flush();

        $view = $this->view(array(
                    'result' => 'success',
                    'new_item_id' => $item->getId(),
                        ), 200)
                ->setFormat('json');

        return $this->handleView($view);
    }

    /**
     * Update existing Item entity.
     *
     */
    public function updateItemAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository('BlueMediaTaskBundle:Item')->find($id);
        $name = $request->query->get('name');
        $stock = $request->query->get('stock');

        if (!$item) {
            $view = $this->view(array(
                        'error_msg' => 'no item found',
                        'result' => 'failure'
                            ), 404)
                    ->setFormat('json');

            return $this->handleView($view);
        }

        if ($name) {
            $item->setName(trim($name));
        }
        if ($stock) {
            $item->setAmount((int) $stock);
        }


        $em->persist($item);
        $em->flush();

        $view = $this->view(array(
                    'result' => 'sucess'
                        ), 200)
                ->setFormat('json');

        return $this->handleView($view);
    }

    /**
     * Deletes a Item entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository('BlueMediaTaskBundle:Item')->find($id);
        if (!$item) {
            $view = $this->view(array(
                        'error_msg' => 'no item found',
                        'result' => 'failure'
                            ), 404)
                    ->setFormat('json');

            return $this->handleView($view);
        }
        $em->remove($item);
        $em->flush();

        $view = $this->view(array(
                    'result' => 'sucess'
                        ), 200)
                ->setFormat('json');

        return $this->handleView($view);
    }

}
