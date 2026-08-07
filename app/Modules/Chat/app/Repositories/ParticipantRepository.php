<?php

namespace Modules\Chat\Repositories;

use App\Core\Repositories\AbstractRepository;
use Modules\Chat\Interfaces\ParticipantRepositoryInterface;
use Modules\Chat\Models\Participant;

class ParticipantRepository extends AbstractRepository implements ParticipantRepositoryInterface{

	public function __construct(
		Participant $model,
	){
		parent::__construct($model);
	}
}
