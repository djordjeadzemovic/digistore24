<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param Request $request
     * @return array<Message>
     * */
    // above anotations are required by PhpStan
    public function fetchData(Request $request): array
    {
        $status = $request->query->get('status');
        /**
         * no need for if else here, neither for the local variable, can be simplified
         * Also, fetching data when status is changed in order to keep return type consisent and always array
         * This works well in this simplified example, filter data by multiple params needs extra work (that is not scope of the task)
         * **/
        if ($status) {
            return $this->findBy(
                ['status' => $status],
            );
        }

        return $this->findAll();
    }
}
