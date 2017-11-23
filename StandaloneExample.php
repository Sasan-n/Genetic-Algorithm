<?php
set_time_limit (0);
ini_set('memory_limit', '1024M'); // or you could use 1G

require_once 'Genetic.class.php';
$Genetic = new GeneticAlgorithm (
    array(
        'Goal' => 'AI',

        'PopulationSize' => 200,     // Size of Population In Each Generation
        'MaxIteration' => 400,
        'GenerationGap' => 100,       // Percent of Checking Fitness Function on Population
        'Age' => 6,
        'Strategy' => 'M,L',
        'Elitism' => true,

        'MinAcceptableFitness' => 0,
        'CodingType' => 'Binary',

        'CrossOverRate' => 100, // Percent
        'MutationRate' => 100,    // Percent
        'InversionRate' => 100,   // Percent

        'CrossOverTechnique' => 1,
        'MutationTechnique' => 1,

        'SingleCrossOverPoint' => 3,
        'TwoCrossOverPoint1' => 2,
        'TwoCrossOverPoint2' => 4,
        'UniformCrossOverRatio' => 50,   // Percent
        'RingCrossOverCuttingPoint' => 3,
        'RingCrossOverCuttingByRandom' => false
    )
);

$Genetic->Run();

echo "<pre>";
print_r(array_reverse($Genetic->Generations));
echo "</pre>";