<?php

namespace Modules\Media\Enums;

enum MediaType: string{

	case IMAGE = 'image';
	case VIDEO = 'video';
	case AUDIO = 'audio';
	case VOICE = 'voice';
	case DOCUMENT = 'document';
	case ARCHIVE = 'archive';
	case OTHER = 'other';
}