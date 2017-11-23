# Genetic-Algorithm

This package can implement genetic algorithm based on 3 reproduction methods :

1. Crossover<br>
2. Mutation<br>
3. Inversion<br>

Each Method has several significant techniques in order to produce their offsprings. Their techniques are :

Crossover<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1.1 Single Point Crossover<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1.2 Two Points Crossover<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1.3 Uniform Crossover<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1.4 Ring Crossover<br>


Mutation<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.1 Bit Flip Mutation<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.2 Random Resetting<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.3 Swap Mutation<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.4 Scramble Mutation<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.5 Inversion Mutation<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.6 Insertion Mutation<br>

Inversion<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 3.1 Inversion all chromosome


Class convert "Goal" into binary coded and try to find binary string.


## Usage

```php
<?php
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
```



## Donate

All work on this class consists of many hours of coding during my free time, to provide you with a usefull Genetic Algorithm Class that is easy to use and extend.
If you enjoy using this class and would like to say thank you, donations are a great way to show your support.

Donations are invested back into the project :+1:

- [PayPal](paypal.me/sasannobakht)
