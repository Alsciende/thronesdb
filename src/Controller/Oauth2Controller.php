<?php

namespace App\Controller;

use App\Entity\DeckInterface;
use App\Entity\Decklist;
use App\Entity\Faction;
use App\Entity\FactionInterface;
use App\Entity\Tournament;
use App\Entity\TournamentInterface;
use App\Entity\UserInterface;
use App\Services\DecklistFactory;
use App\Services\DeckManager;
use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use App\Entity\Deck;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller
 */
class Oauth2Controller extends AbstractController
{
    /**
     * @Route("/api/oauth2/user", name="api_oauth2_user", methods={"GET"}, options={"i18n" = false})
     * @return Response
     */
    public function userAction()
    {
        $response = new Response();
        $response->headers->add(['Access-Control-Allow-Origin' => '*']);

        /** @var UserInterface $user */
        $user = $this->getUser();
        $data = [
            'id'         => $user->getId(),
            'username'   => $user->getUsername(),
            'email'      => $user->getEmail(),
            'reputation' => $user->getReputation(),
        ];
        $content = json_encode([
            'data' => [$data],
        ]);

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($content);

        return $response;
    }

    /**
     * Get the description of all the Decks of the authenticated user
     *
     * @Route("/api/oauth2/decks", name="api_oauth2_decks", methods={"GET"}, options={"i18n" = false})
     *
     * @Operation(
     *     tags={"Protected"},
     *     summary="All the Decks",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function listDecksAction(Request $request)
    {
        $response = new Response();
        $response->headers->add(['Access-Control-Allow-Origin' => '*']);

        /* @var $decks DeckInterface[] */
        $decks = $this->getDoctrine()->getRepository(Deck::class)->findBy(['user' => $this->getUser()]);

        if (! empty($decks)) {
            $dateUpdates = array_map(function (DeckInterface $deck) {
                return $deck->getDateUpdate();
            }, $decks);

            $response->setLastModified(max($dateUpdates));
            if ($response->isNotModified($request)) {
                return $response;
            }
        }

        $content = json_encode($decks);

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($content);

        return $response;
    }


    /**
     * Get the description of one Deck of the authenticated user
     *
     * @Route(
     *     "/api/oauth2/deck/load/{id}",
     *     name="api_oauth2_load_deck",
     *     methods={"GET"},
     *     requirements={"id"="\d+"},
     *     options={"i18n" = false}
     * )
     *
     * @Operation(
     *     tags={"Protected"},
     *     summary="Load One Deck",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function loadDeckAction(Request $request, $id)
    {
        $response = new Response();
        $response->headers->add(['Access-Control-Allow-Origin' => '*']);

        /* @var DeckInterface $deck */
        $deck = $this->getDoctrine()->getRepository(Deck::class)->find($id);

        if ($deck->getUser()->getId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException("Access denied to this object.");
        }

        $response->setLastModified($deck->getDateUpdate());
        if ($response->isNotModified($request)) {
            return $response;
        }

        $content = json_encode($deck);

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($content);

        return $response;
    }


    /**
     * Save one Deck of the authenticated user. The parameters are the same as in the response to the load method,
     * but only a few are writable.
     * So you can parse the result from the load, change a few values,
     * then send the object as the param of an ajax request.
     * If successful, id of Deck is in the msg.
     *
     * @Route(
     *     "/api/oauth2/deck/save/{id}",
     *     name="api_oauth2_save_deck",
     *     methods={"PUT"},
     *     requirements={"id"="\d+"},
     *     options={"i18n" = false}
     * )
     *
     * @Operation(
     *     tags={"Protected"},
     *     summary="Save One Deck",
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="Name of the Deck",
     *         required=true,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="decklist_id",
     *         in="body",
     *         description="Identifier of the Decklist from which the Deck is copied",
     *         required=false,
     *         @SWG\Schema(type="integer")
     *     ),
     *     @SWG\Parameter(
     *         name="description_md",
     *         in="body",
     *         description="Description of the Decklist in Markdown",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="faction_code",
     *         in="body",
     *         description="Code of the faction of the Deck",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="tags",
     *         in="body",
     *         description="Space-separated list of tags",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="slots",
     *         in="body",
     *         description="Content of the Decklist as a JSON object",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     *
     * @param int $id
     * @param Request $request
     * @param DeckManager $deckManager
     */
    public function saveDeckAction($id, Request $request, DeckManager $deckManager)
    {
        if (!$id) {
            $deck = new Deck();
            $deck->setUuid(Uuid::uuid4());
            $this->getDoctrine()->getManager()->persist($deck);
        } else {
            $deck = $this->getDoctrine()->getRepository(Deck::class)->find($id);
            if ($deck->getUser()->getId() !== $this->getUser()->getId()) {
                throw $this->createAccessDeniedException("Access denied to this object.");
            }
        }

        $faction_code = filter_var(
            $request->get('faction_code'),
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_NO_ENCODE_QUOTES
        );
        if (!$faction_code) {
            return new JsonResponse([
                'success' => false,
                'msg'     => "Faction code missing",
            ]);
        }
        /* @var FactionInterface $faction */
        $faction = $this->getDoctrine()
            ->getManager()
            ->getRepository(Faction::class)
            ->findOneBy(['code' => $faction_code]);
        if (!$faction) {
            return new JsonResponse([
                'success' => false,
                'msg'     => "Faction code invalid",
            ]);
        }

        $slots = (array) json_decode($request->get('slots'));
        if (!count($slots)) {
            return new JsonResponse([
                'success' => false,
                'msg'     => "Slots missing",
            ]);
        }
        foreach ($slots as $card_code => $qty) {
            if (!is_string($card_code) || !is_integer($qty)) {
                return new JsonResponse([
                    'success' => false,
                    'msg'     => "Slots invalid",
                ]);
            }
        }

        $name = filter_var(
            $request->get('name'),
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_NO_ENCODE_QUOTES
        );
        if (!$name) {
            return new JsonResponse([
                'success' => false,
                'msg'     => "Name missing",
            ]);
        }

        $decklist_id = filter_var($request->get('decklist_id'), FILTER_SANITIZE_NUMBER_INT);
        $description = trim($request->get('description'));
        $tags = filter_var($request->get('tags'), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

        $deckManager->save(
            $this->getUser(),
            $deck,
            $decklist_id,
            $name,
            $faction,
            $description,
            $tags,
            $slots,
            null
        );

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'success' => true,
            'msg'     => $deck->getId(),
        ]);
    }

    /**
     * Try to publish one Deck of the authenticated user
     * If publication is successful, update the version of the deck and return the id of the decklist
     *
     * @Route(
     *     "/api/oauth2/deck/publish/{id}",
     *     name="api_oauth2_publish_deck",
     *     methods={"PUT"},
     *     requirements={"id"="\d+"},
     *     options={"i18n" = false}
     * )
     *
     * @Operation(
     *     tags={"Protected"},
     *     summary="Publish One Deck",
     *     @SWG\Parameter(
     *         name="description_md",
     *         in="body",
     *         description="Description of the Decklist in Markdown",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="tournament_id",
     *         in="body",
     *         description="Identifier of the Tournament type of the Decklist",
     *         required=false,
     *         @SWG\Schema(type="integer")
     *     ),
     *     @SWG\Parameter(
     *         name="precedent_id",
     *         in="body",
     *         description="Identifier of the Predecessor of the Decklist",
     *         required=false,
     *         @SWG\Schema(type="integer")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     *
     * @param int $id
     * @param Request $request
     * @param DecklistFactory $decklistFactory
     * @return Response $response
     */
    public function publishDeckAction($id, Request $request, DecklistFactory $decklistFactory)
    {
        /* @var DeckInterface $deck */
        $deck = $this->getDoctrine()->getRepository(Deck::class)->find($id);
        if ($this->getUser()->getId() !== $deck->getUser()->getId()) {
            throw $this->createAccessDeniedException("Access denied to this object.");
        }

        $name = filter_var(
            $request->request->get('name'),
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_NO_ENCODE_QUOTES
        );
        $descriptionMd = trim($request->request->get('description_md'));

        $tournament_id = intval(
            filter_var($request->request->get('tournament_id'), FILTER_SANITIZE_NUMBER_INT)
        );
        /* @var TournamentInterface $tournament */
        $tournament = $this->getDoctrine()->getManager()->getRepository(Tournament::class)->find($tournament_id);

        $precedent_id = trim($request->request->get('precedent'));
        if (!preg_match('/^\d+$/', $precedent_id)) {
            // route decklist_detail hard-coded
            if (preg_match('/view\/(\d+)/', $precedent_id, $matches)) {
                $precedent_id = $matches[1];
            } else {
                $precedent_id = null;
            }
        }
        $precedent = $precedent_id
            ? $this->getDoctrine()->getRepository(Decklist::class)->find($precedent_id)
            : null;

        try {
            $decklist = $decklistFactory->createDecklistFromDeck($deck, $name, $descriptionMd);
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'msg'     => $e->getMessage(),
            ]);
        }

        $decklist->setTournament($tournament);
        $decklist->setPrecedent($precedent);
        $this->getDoctrine()->getManager()->persist($decklist);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'success' => true,
            'msg'     => $decklist->getId(),
        ]);
    }
}
