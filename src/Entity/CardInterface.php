<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Exception;
use Serializable;

/**
 * Interface CardInterface
 * @package App\Entity
 */
interface CardInterface extends Serializable
{
    /**
     * @param int $id
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $position
     */
    public function setPosition($position);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $cost
     */
    public function setCost($cost);

    /**
     * @return string
     */
    public function getCost();

    /**
     * @param string $text
     */
    public function setText($text);

    /**
     * @return string
     */
    public function getText();

    /**
     * @param DateTime $dateCreation
     */
    public function setDateCreation($dateCreation);

    /**
     * @return DateTime
     */
    public function getDateCreation();

    /**
     * @param DateTime $dateUpdate
     */
    public function setDateUpdate($dateUpdate);

    /**
     * @return DateTime
     */
    public function getDateUpdate();

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param int $income
     */
    public function setIncome($income);

    /**
     * @return int
     */
    public function getIncome();

    /**
     * @param int $initiative
     */
    public function setInitiative($initiative);

    /**
     * @return int
     */
    public function getInitiative();

    /**
     * @param int $claim
     */
    public function setClaim($claim);

    /**
     * @return int
     */
    public function getClaim();

    /**
     * @param int $reserve
     */
    public function setReserve($reserve);

    /**
     * @return int
     */
    public function getReserve();

    /**
     * @param int $deckLimit
     */
    public function setDeckLimit($deckLimit);

    /**
     * @return int
     */
    public function getDeckLimit();

    /**
     * @param int $strength
     */
    public function setStrength($strength);

    /**
     * @return int
     */
    public function getStrength();

    /**
     * @param string $traits
     */
    public function setTraits($traits);

    /**
     * @return string
     */
    public function getTraits();

    /**
     * @param string $flavor
     */
    public function setFlavor($flavor);

    /**
     * @return string
     */
    public function getFlavor();

    /**
     * @param string $illustrator
     */
    public function setIllustrator($illustrator);

    /**
     * @return string
     */
    public function getIllustrator();

    /**
     * @param bool $isUnique
     */
    public function setIsUnique($isUnique);

    /**
     * @return bool
     */
    public function getIsUnique();

    /**
     * @param bool $isLoyal
     */
    public function setIsLoyal($isLoyal);

    /**
     * @return bool
     */
    public function getIsLoyal();

    /**
     * @param bool $isMilitary
     */
    public function setIsMilitary($isMilitary);

    /**
     * @return bool
     */
    public function getIsMilitary();

    /**
     * @param bool $isIntrigue
     */
    public function setIsIntrigue($isIntrigue);

    /**
     * @return bool
     */
    public function getIsIntrigue();

    /**
     * @param bool $isPower
     */
    public function setIsPower($isPower);

    /**
     * @return bool
     */
    public function getIsPower();

    /**
     * @param bool $octgnId
     */
    public function setOctgnId($octgnId);

    /**
     * @return bool
     */
    public function getOctgnId();

    /**
     * @param Review $review
     */
    public function addReview(Review $review);

    /**
     * @param Review $review
     */
    public function removeReview(Review $review);

    /**
     * @return Collection
     */
    public function getReviews();

    /**
     * @param Pack $pack
     */
    public function setPack(Pack $pack = null);

    /**
     * @return Pack
     */
    public function getPack();

    /**
     * @param Type $type
     */
    public function setType(Type $type = null);

    /**
     * @return Type
     */
    public function getType();

    /**
     * @param Faction $faction
     */
    public function setFaction(Faction $faction = null);

    /**
     * @return Faction
     */
    public function getFaction();

    /**
     * @return string
     */
    public function getCostIncome();

    /**
     * @return int
     */
    public function getStrengthInitiative();

    /**
     * @param string $designer
     */
    public function setDesigner($designer);

    /**
     * @return string
     */
    public function getDesigner();

    /**
     * @return bool
     */
    public function getIsMultiple(): bool;

    /**
     * @param bool $isMultiple
     */
    public function setIsMultiple(bool $isMultiple);

    /**
     * @return string|null
     */
    public function getImageUrl();

    /**
     * @param string $imageUrl
     */
    public function setImageUrl(string $imageUrl);

    /**
     * Checks if this card has the "Shadow" keyword.
     * @param string $shadow The keyword "Shadow" in whatever language.
     * @return bool
     */
    public function hasShadowKeyword($shadow): bool;
}