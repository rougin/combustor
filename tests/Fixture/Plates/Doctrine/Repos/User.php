<?php

use Rougin\Credo\Repository;

/**
 * @extends \Rougin\Credo\Repository<\User>
 *
 * @property \CI_DB_query_builder $db
 *
 * @method void       create(array<string, mixed> $data, \User $entity)
 * @method void       delete(\User $entity)
 * @method \User|null find(integer $id)
 * @method \User[]    get(integer|null $limit = null, integer|null $offset = null)
 * @method \User      set(array<string, mixed> $data, \User $entity, integer|null $id = null)
 * @method void       update(\User $entity, array<string, mixed> $data)
 */
class User_repository extends Repository
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
     * @param \User                $entity
     * @param integer|null         $id
     *
     * @return User
     */
    public function set($data, $entity, $id = null)
    {
        // List editable fields from table ---
        /** @var string */
        $email = $data['email'];
        $entity->set_email($email);

        /** @var string */
        $name = $data['name'];
        $entity->set_name($name);

        /** @var integer */
        $year = $data['year'];
        $entity->set_year($year);

        /** @var boolean */
        $admin = $data['admin'];
        $entity->set_admin($admin);

        /** @var string|null */
        $remarks = $data['remarks'];
        if ($remarks)
        {
            $entity->set_remarks($remarks);
        }
        // -----------------------------------

        return $entity;
    }
}
