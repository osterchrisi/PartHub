<?php declare(strict_types = 1);

namespace ApiGen\Info;


enum ErrorKind
{
	case SyntaxError;
	case InvalidEncoding;
	case MissingSymbol;
	case DuplicateSymbol;
}
