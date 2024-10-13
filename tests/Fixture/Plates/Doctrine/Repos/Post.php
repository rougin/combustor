<?php

use Rougin\Credo\Repository;

/**
 * @extends \Rougin\Credo\Repository<\Post>
 *
 * @property \CI_DB_query_builder                 $db
 * @property \Doctrine\ORM\EntityManagerInterface $_em
 *
 * @method void       create(array<string, mixed> $data, \Post $entity)
 * @method void       delete(\Post $entity)
 * @method \Post|null find(integer $id)
 * @method \Post[]    get(integer|null $limit = null, integer|null $offset = null)
 * @method \Post      set(array<string, mixed> $data, \Post $entity, integer|null $id = null)
 * @method void       update(\Post $entity, array<string, mixed> $data)
 */
class Post_repository extends Repository
{
    /**
     * @param array<string, mixed> $data
     * @param integer|null         $id
     *
     * @return boolean
     */
    public function exists($data, $id = null)
    {
        // Specify logic here if applicable ---
        // ------------------------------------

        return false;
    }

    /**
     * @param array<string, mixed> $data
     * @param \Post                $entity
     * @param integer|null         $id
     *
     * @return \Post
     */
    public function set($data, $entity, $id = null)
    {
        // List editable fields from table ---
        /** @var string */
        $title = $data['title'];
        $entity->set_title($title);

        /** @var string */
        $text = $data['text'];
        $entity->set_text($text);

        /** @var integer|null */
        $user_id = $data['user_id'];
        if ($user_id)
        {
            $user = $this->_em->find('User', $user_id);
            $entity->set_user($user);
        }
        // -----------------------------------

        return $entity;
    }
}
