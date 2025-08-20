<?php

enum OperationType: string{
    case Transfer = 'planning';
    case Production = 'production';
    case Sale = 'sale';
    case Elaboration = 'elaboration';
}

