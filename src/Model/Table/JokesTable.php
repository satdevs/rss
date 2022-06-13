<?php
// Baked at 2022.03.17. 08:53:32
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Jokes Model
 *
 * @method \App\Model\Entity\Joke newEmptyEntity()
 * @method \App\Model\Entity\Joke newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Joke[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Joke get($primaryKey, $options = [])
 * @method \App\Model\Entity\Joke findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Joke patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Joke[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Joke|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Joke saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Joke[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Joke[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Joke[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Joke[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class JokesTable extends Table
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

        $this->setTable('jokes');
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('title')
            ->maxLength('title', 250)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('body')
            ->requirePresence('body', 'create')
            ->notEmptyString('body');

        $validator
            ->requirePresence('current', 'create')
            ->notEmptyString('current');

        $validator
            ->nonNegativeInteger('counter')
            ->requirePresence('counter', 'create')
            ->notEmptyString('counter');

        return $validator;
    }
}
