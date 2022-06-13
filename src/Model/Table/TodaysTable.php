<?php
// Baked at 2022.03.17. 09:14:38
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Todays Model
 *
 * @method \App\Model\Entity\Today newEmptyEntity()
 * @method \App\Model\Entity\Today newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Today[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Today get($primaryKey, $options = [])
 * @method \App\Model\Entity\Today findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Today patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Today[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Today|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Today saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Today[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Today[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Today[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Today[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TodaysTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('todays');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('title')
            ->maxLength('title', 250)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('text')
            ->requirePresence('text', 'create')
            ->notEmptyString('text');

        $validator
            ->integer('year')
            ->requirePresence('year', 'create')
            ->notEmptyString('year');

        $validator
            ->nonNegativeInteger('month')
            ->requirePresence('month', 'create')
            ->notEmptyString('month');

        $validator
            ->nonNegativeInteger('day')
            ->requirePresence('day', 'create')
            ->notEmptyString('day');

        $validator
            ->dateTime('datetime')
            ->requirePresence('datetime', 'create')
            ->notEmptyDateTime('datetime');

        $validator
            ->scalar('pubdate')
            ->maxLength('pubdate', 40)
            ->requirePresence('pubdate', 'create')
            ->notEmptyString('pubdate');

        $validator
            ->nonNegativeInteger('counter')
            ->requirePresence('counter', 'create')
            ->notEmptyString('counter');

        return $validator;
    }
}
