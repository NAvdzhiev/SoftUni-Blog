<?php

namespace SoftUniBlogBundle\Repository;

/**
 * CommentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommentRepository extends \Doctrine\ORM\EntityRepository
{
    public function getCommentsForBlog($articleId, $approved = true)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.articles = :article_id')
            ->addOrderBy('c.created')
            ->setParameter('article_id', $articleId);

        if (false === is_null($approved))
            $qb->andWhere('c.approved = :approved')
                ->setParameter('approved', $approved);

        return $qb->getQuery()
            ->getResult();
    }

    public function getLatestComments($limit = 10)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->addOrderBy('c.id', 'DESC');

        if (false === is_null($limit))
            $qb->setMaxResults($limit);

        return $qb->getQuery()
            ->getResult();
    }
}