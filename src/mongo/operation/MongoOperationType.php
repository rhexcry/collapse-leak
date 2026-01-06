<?php

declare(strict_types=1);

namespace collapse\mongo\operation;

enum MongoOperationType{

	case FindOne;
	case Find;
	case InsertOne;
	case ReplaceMany;
	case ReplaceOne;
	case UpdateOne;
	case DeleteMany;
}
