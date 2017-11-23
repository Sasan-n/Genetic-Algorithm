<?php

class GeneticAlgorithm {
    public $Options = array();
    public $Options_default = array(
        'Debug' => false,
        'Seed' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'Goal' => 'Sasan',

        'PopulationSize' => 200,     // Size of Population In Each Generation
        'MaxIteration' => 400,
        'GenerationGap' => 100,       // Percent of Checking Fitness Function on Population
        'Age' => 2,
        'Strategy' => 'M,L',
        'MaxPopulationSizeM+L' => '100',
        'NormPopulationSizeM+L' => '2',
        'Elitism' => false,

        'MinAcceptableFitness' => 0,
        'CodingType' => 'Gray',

        'CrossOverRate' => 100, // Percent
        'MutationRate' => 10,    // Percent
        'InversionRate' => 10,   // Percent

        'CrossOverTechnique' => 1,
        'MutationTechnique' => 1,

        'SingleCrossOverPoint' => false,
        'TwoCrossOverPoint1' => false,
        'TwoCrossOverPoint2' => false,
        'UniformCrossOverRatio' => 50,   // Percent
        'RingCrossOverCuttingPoint' => false,
        'RingCrossOverCuttingByRandom' => false
    );

    public $CharTable   = array();
    public $Generations = array();
    public $Population  = array();
    public $LastNormPopulationSizeMplusL = 0;

    public $TwoParents  = array();

    function __construct($Initialization = array()) {
        $this->Options = array_merge($this->Options_default, $Initialization);

        for($i = 0; $i < strlen($this->Options['Seed']); $i++){
            $this->CharTable[$this->Options['Seed'][$i]] = $this->CharToBin($this->Options['Seed'][$i],$this->Options['CodingType']);
        }

        $BinGoal = "";
        for($i = 0; $i < strlen($this->Options['Goal']); $i++){
            $BinGoal .=  $this->CharTable[$this->Options['Goal'][$i]];
        }
        $this->Options['Goal'] = $BinGoal;

        $this->LastNormPopulationSizeMplusL = $this->Options['NormPopulationSizeM+L'];

        if ($this->Options['CrossOverTechnique'] == "0") $this->Options['CrossOverTechnique'] = rand(2,5);
        if ($this->Options['MutationTechnique'] == "0") $this->Options['MutationTechnique'] = rand(2,7);

        if (!$this->Options['SingleCrossOverPoint']) $this->Options['SingleCrossOverPoint'] = rand(1,strlen($this->Options['Goal'])-1);

        if (!$this->Options['TwoCrossOverPoint1']) $this->Options['TwoCrossOverPoint1'] = rand(1,((strlen($this->Options['Goal'])/2)-1));
        if (!$this->Options['TwoCrossOverPoint2']) $this->Options['TwoCrossOverPoint2'] = rand(((strlen($this->Options['Goal'])/2)-1),(strlen($this->Options['Goal'])-1));

        if (!$this->Options['UniformCrossOverRatio']) $this->Options['UniformCrossOverRatio'] = rand(0,100);

        if (!$this->Options['RingCrossOverCuttingPoint']) $this->Options['RingCrossOverCuttingPoint'] = rand(0,strlen($this->Options['Goal'])*2);
    }

    function Generate_New_Generation() {
        $NewPopulation = array();

        if (count($this->Generations) == 0) {
            for($i = 0; $i < $this->Options['PopulationSize']; $i++){
                $Chromosome = "";
                for ($j = 0; $j < strlen($this->Options['Goal']); $j++) {
                    $Chromosome .= rand(0, 1);
                }

                array_push($NewPopulation,
                    array(
                        'Parent1'    => "",
                        'Parent2'    => "",
                        'CAction'    => "",
                        'MAction'    => "",
                        'IAction'    => "",
                        'Chromosome' => $Chromosome,
                        'Text'       => $this->BinToString($Chromosome,$this->Options['CodingType']),
                        'Fitness'    => strlen($Chromosome)*2,
                        'Age'        => 0
                    )
                );
            }
        }
        else
        {
            if ($this->Options['Elitism'] == "true") {
                $Bests = $this->GetBestGene();
                foreach($Bests as $Best){
                    array_push($NewPopulation,$Best);
                }
            }

            if ($this->Options['Strategy'] == "M,L") {
                while (count($NewPopulation) < $this->Options['PopulationSize']) {
                    // Tunnel
                    $this->SelectGenes();
                    $this->Crossover();
                    $this->Mutation();
                    $this->Inversion();

                    foreach($this->TwoParents as $FinalNewGene) {
                        array_push($NewPopulation,$FinalNewGene);
                    }
                }
            }
            else if ($this->Options['Strategy'] == "M+L")
            {
                while (count($NewPopulation) < $this->Options['PopulationSize']+$this->LastNormPopulationSizeMplusL && count($NewPopulation) < $this->Options['MaxPopulationSizeM+L']) {
                    // Tunnel
                    $this->SelectGenes();
                    $this->Crossover();
                    $this->Mutation();
                    $this->Inversion();

                    foreach($this->TwoParents as $FinalNewGene) {
                        array_push($NewPopulation,$FinalNewGene);
                    }
                }
                $this->LastNormPopulationSizeMplusL += $this->Options['NormPopulationSizeM+L'];
            }
        }

        $this->Population = $NewPopulation;
    }
    function FitnessFunction($Member) {
        $Chromosome = $this->Population[$Member]['Chromosome'];

        $this->Population[$Member]['Fitness'] = strlen($Chromosome);

        for($j = 0; $j < strlen($Chromosome); $j++){
            if ($Chromosome[$j] == $this->Options['Goal'][$j])
                $this->Population[$Member]['Fitness']--;
        }

        $this->Population[$Member]['Age']++;
    }
    function Evaluation() {
        // Do Fitness Function on All Members
        for($i = 0; $i < count($this->Population); $i++) {
            if ($this->Population[$i]['Age'] >= $this->Options['Age']) {
                // Age >  ===> Delete Member
                unset($this->Population[$i]);


            } else if (rand(1,100) <= $this->Options['GenerationGap']) {
                $this->FitnessFunction($i);
            }
        }

        // Sort Population By Their Fitness
        $this->Population = $this->array_order_by($this->Population, 'Fitness', SORT_ASC, 'Age', SORT_DESC);

        // Delete Repeated
        $Chromosomes = array();
        for($i = 0; $i < count($this->Population); $i++) {
            if (in_array($this->Population[$i]['Chromosome'],$Chromosomes)) {
                unset($this->Population[$i]);
            }
            @array_push($Chromosomes,$this->Population[$i]['Chromosome']);
        }

        $this->Population = array_values($this->Population);

        // Store Generation
        $this->Generations[count($this->Generations)] = $this->Population;
    }
    function GetBestGene() {
        $Bests = array();

        // Scan Population for Repeated Best Fitness
        for($i = 0; $i < count($this->Population); $i++) {
            if ($i == 0 /*|| $this->Population[$i]['Fitness'] == $this->Population[0]['Fitness']*/)
                array_push($Bests,$this->Population[$i]);
        }

        return $Bests;
    }
    function SelectGenes() {
        $GenerationIndex = count($this->Generations)-1;
        $GenerationIndexPopulation = count($this->Generations[$GenerationIndex])-1;

        $this->TwoParents[0] = $this->Generations[$GenerationIndex][rand(0,$GenerationIndexPopulation)];
        $this->TwoParents[1] = $this->Generations[$GenerationIndex][rand(0,$GenerationIndexPopulation)];
    }


    function Crossover() {
        if (rand(1,100) <= $this->Options['CrossOverRate']) {
            if ($this->Options['CrossOverTechnique'] == "1") {
                $CrossOverTechnique = rand(2,5);
            } else {
                $CrossOverTechnique = $this->Options['CrossOverTechnique'];
            }

            $Chromosome[0] = $this->TwoParents[0]['Chromosome'];
            $Chromosome[1] = $this->TwoParents[1]['Chromosome'];

            if ($CrossOverTechnique == 2)     {
                $NewChromosome[0] = substr($Chromosome[0], 0, $this->Options['SingleCrossOverPoint']) . substr($Chromosome[1], $this->Options['SingleCrossOverPoint'], strlen($Chromosome[1]));
                $NewChromosome[1] = substr($Chromosome[1], 0, $this->Options['SingleCrossOverPoint']) . substr($Chromosome[0], $this->Options['SingleCrossOverPoint'], strlen($Chromosome[0]));

                $this->TwoParents[0] = array(
                    'CAction'           => array(
                        'Parent 0'      => substr($Chromosome[0], 0, $this->Options['SingleCrossOverPoint']) ." ". substr($Chromosome[0], $this->Options['SingleCrossOverPoint'], strlen($Chromosome[0])),
                        'Parent 1'      => substr($Chromosome[1], 0, $this->Options['SingleCrossOverPoint']) ." ". substr($Chromosome[1], $this->Options['SingleCrossOverPoint'], strlen($Chromosome[1])),
                        'Type'          => 'SinglePoint',
                        'Point'         => $this->Options['SingleCrossOverPoint'],
                        'NEW Parent 0'  => substr($NewChromosome[0], 0, $this->Options['SingleCrossOverPoint']) ." ". substr($NewChromosome[0], $this->Options['SingleCrossOverPoint'], strlen($NewChromosome[0])),
                        'NEW Parent 1'  => substr($NewChromosome[1], 0, $this->Options['SingleCrossOverPoint']) ." ". substr($NewChromosome[1], $this->Options['SingleCrossOverPoint'], strlen($NewChromosome[1]))
                    ),
                    'MAction'           => "",
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[0],
                    'Text'              => $this->BinToString($NewChromosome[0],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[0]) * 2,
                    'Age'               => 0
                );


                $this->TwoParents[1] = array(
                    'CAction'           => array(
                        'Parent 0'      => substr($Chromosome[0], 0, $this->Options['SingleCrossOverPoint']) ." ". substr($Chromosome[0], $this->Options['SingleCrossOverPoint'], strlen($Chromosome[0])),
                        'Parent 1'      => substr($Chromosome[1], 0, $this->Options['SingleCrossOverPoint']) ." ". substr($Chromosome[1], $this->Options['SingleCrossOverPoint'], strlen($Chromosome[1])),
                        'Type'          => 'SinglePoint',
                        'Point'         => $this->Options['SingleCrossOverPoint'],
                        'NEW Parent 0'  => substr($NewChromosome[0], 0, $this->Options['SingleCrossOverPoint']) ." ". substr($NewChromosome[0], $this->Options['SingleCrossOverPoint'], strlen($NewChromosome[0])),
                        'NEW Parent 1'  => substr($NewChromosome[1], 0, $this->Options['SingleCrossOverPoint']) ." ". substr($NewChromosome[1], $this->Options['SingleCrossOverPoint'], strlen($NewChromosome[1]))
                    ),
                    'MAction'           => "",
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[1],
                    'Text'              => $this->BinToString($NewChromosome[1],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[1]) * 2,
                    'Age'               => 0
                );




            }
            else if ($CrossOverTechnique == 3)
            {
                $NewChromosome[0] = substr($Chromosome[0], 0, $this->Options['TwoCrossOverPoint1']) . substr($Chromosome[1], $this->Options['TwoCrossOverPoint1'], ($this->Options['TwoCrossOverPoint2']-$this->Options['TwoCrossOverPoint1'])). substr($Chromosome[0], $this->Options['TwoCrossOverPoint2'], strlen($Chromosome[0]));
                $NewChromosome[1] = substr($Chromosome[1], 0, $this->Options['TwoCrossOverPoint1']) . substr($Chromosome[0], $this->Options['TwoCrossOverPoint1'], ($this->Options['TwoCrossOverPoint2']-$this->Options['TwoCrossOverPoint1'])). substr($Chromosome[1], $this->Options['TwoCrossOverPoint2'], strlen($Chromosome[1]));

                $this->TwoParents[0] = array(
                    'CAction'           => array(
                        'Parent 0'      => substr($Chromosome[0], 0, $this->Options['TwoCrossOverPoint1']) ." ".substr($Chromosome[0], $this->Options['TwoCrossOverPoint1'], ($this->Options['TwoCrossOverPoint2']-$this->Options['TwoCrossOverPoint1'])) ." ". substr($Chromosome[0], $this->Options['TwoCrossOverPoint2'], strlen($Chromosome[0])),
                        'Parent 1'      => substr($Chromosome[1], 0, $this->Options['TwoCrossOverPoint1']) ." ".substr($Chromosome[1], $this->Options['TwoCrossOverPoint1'], ($this->Options['TwoCrossOverPoint2']-$this->Options['TwoCrossOverPoint1'])) ." ". substr($Chromosome[1], $this->Options['TwoCrossOverPoint2'], strlen($Chromosome[1])),
                        'Type'          => 'TwoPoint',
                        'Point1'        => $this->Options['TwoCrossOverPoint1'],
                        'Point2'        => $this->Options['TwoCrossOverPoint2'],
                        'NEW Parent 0'  => substr($NewChromosome[0], 0, $this->Options['TwoCrossOverPoint1']) ." ".substr($NewChromosome[0], $this->Options['TwoCrossOverPoint1'], ($this->Options['TwoCrossOverPoint2']-$this->Options['TwoCrossOverPoint1'])) ." ". substr($NewChromosome[0], $this->Options['TwoCrossOverPoint2'], strlen($NewChromosome[0])),
                        'NEW Parent 1'  => substr($NewChromosome[1], 0, $this->Options['TwoCrossOverPoint1']) ." ".substr($NewChromosome[1], $this->Options['TwoCrossOverPoint1'], ($this->Options['TwoCrossOverPoint2']-$this->Options['TwoCrossOverPoint1'])) ." ". substr($NewChromosome[1], $this->Options['TwoCrossOverPoint2'], strlen($NewChromosome[1]))
                    ),
                    'MAction'           => "",
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[0],
                    'Text'              => $this->BinToString($NewChromosome[0],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[0]) * 2,
                    'Age'               => 0
                );


                $this->TwoParents[1] = array(
                    'CAction'           => array(
                        'Parent 0'      => substr($Chromosome[0], 0, $this->Options['TwoCrossOverPoint1']) ." ".substr($Chromosome[0], $this->Options['TwoCrossOverPoint1'], ($this->Options['TwoCrossOverPoint2']-$this->Options['TwoCrossOverPoint1'])) ." ". substr($Chromosome[0], $this->Options['TwoCrossOverPoint2'], strlen($Chromosome[0])),
                        'Parent 1'      => substr($Chromosome[1], 0, $this->Options['TwoCrossOverPoint1']) ." ".substr($Chromosome[1], $this->Options['TwoCrossOverPoint1'], ($this->Options['TwoCrossOverPoint2']-$this->Options['TwoCrossOverPoint1'])) ." ". substr($Chromosome[1], $this->Options['TwoCrossOverPoint2'], strlen($Chromosome[1])),
                        'Type'          => 'TwoPoint',
                        'Point1'        => $this->Options['TwoCrossOverPoint1'],
                        'Point2'        => $this->Options['TwoCrossOverPoint2'],
                        'NEW Parent 0'  => substr($NewChromosome[0], 0, $this->Options['TwoCrossOverPoint1']) ." ".substr($NewChromosome[0], $this->Options['TwoCrossOverPoint1'], ($this->Options['TwoCrossOverPoint2']-$this->Options['TwoCrossOverPoint1'])) ." ". substr($NewChromosome[0], $this->Options['TwoCrossOverPoint2'], strlen($NewChromosome[0])),
                        'NEW Parent 1'  => substr($NewChromosome[1], 0, $this->Options['TwoCrossOverPoint1']) ." ".substr($NewChromosome[1], $this->Options['TwoCrossOverPoint1'], ($this->Options['TwoCrossOverPoint2']-$this->Options['TwoCrossOverPoint1'])) ." ". substr($NewChromosome[1], $this->Options['TwoCrossOverPoint2'], strlen($NewChromosome[1]))
                    ),
                    'MAction'           => "",
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[1],
                    'Text'              => $this->BinToString($NewChromosome[1],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[1]) * 2,
                    'Age'               => 0
                );




            }
            else if ($CrossOverTechnique == 4)
            {

                $NewChromosome[0] = "";
                $NewChromosome[1] = "";

                $ChromosomeA = str_split($Chromosome[0]);
                $ChromosomeB = str_split($Chromosome[1]);

                for($i = 0; $i < strlen($Chromosome[0]); $i++) {
                    if (rand(1,100) <= $this->Options['UniformCrossOverRatio']) {
                        $NewChromosome[0] .= $Chromosome[0][$i];
                        $ChromosomeA[$i] = "[".$ChromosomeA[$i]."]";
                    } else {
                        $NewChromosome[0] .= $Chromosome[1][$i];
                        $ChromosomeA[$i] = " ".$ChromosomeA[$i]." ";
                    }
                    if (rand(1,100) <= $this->Options['UniformCrossOverRatio']) {
                        $NewChromosome[1] .= $Chromosome[1][$i];
                        $ChromosomeB[$i] = "[".$ChromosomeB[$i]."]";
                    } else {
                        $NewChromosome[1] .= $Chromosome[0][$i];
                        $ChromosomeB[$i] = " ".$ChromosomeB[$i]." ";
                    }
                }

                $Chromosome[0] = implode($ChromosomeA);
                $Chromosome[1] = implode($ChromosomeB);

                $this->TwoParents[0] = array(
                    'CAction'           => array(
                        'Parent 0'      => $Chromosome[0],
                        'Parent 1'      => $Chromosome[1],
                        'Type'          => 'Uniform',
                        'Ratio'         => $this->Options['UniformCrossOverRatio'],
                        'NEW Parent 0'  => $NewChromosome[0],
                        'NEW Parent 1'  => $NewChromosome[1]
                    ),
                    'MAction'           => "",
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[0],
                    'Text'              => $this->BinToString($NewChromosome[0],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[0]) * 2,
                    'Age'               => 0
                );


                $this->TwoParents[1] = array(
                    'CAction'           => array(
                        'Parent 0'      => $Chromosome[0],
                        'Parent 1'      => $Chromosome[1],
                        'Type'          => 'Uniform',
                        'Ratio'         => $this->Options['UniformCrossOverRatio'],
                        'NEW Parent 0'  => $NewChromosome[0],
                        'NEW Parent 1'  => $NewChromosome[1]
                    ),
                    'MAction'           => "",
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[1],
                    'Text'              => $this->BinToString($NewChromosome[1],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[1]) * 2,
                    'Age'               => 0
                );


            }
            else if ($CrossOverTechnique == 5)
            {
                if ($this->Options['RingCrossOverCuttingByRandom'] == "true") {
                    $this->Options['RingCrossOverCuttingPoint'] = rand(0,(strlen($Chromosome[0])+strlen($Chromosome[1])-1));
                }

                $ChromosomeC = $Chromosome[0].$Chromosome[1];

                if ($this->Options['RingCrossOverCuttingPoint'] < strlen($Chromosome[0])) {
                    $NewChromosome[0] = substr($ChromosomeC, $this->Options['RingCrossOverCuttingPoint']+strlen($Chromosome[0]), strlen($ChromosomeC));
                    $NewChromosome[0] .= substr($ChromosomeC, 0, $this->Options['RingCrossOverCuttingPoint']);
                    $NewChromosome[1] = substr($ChromosomeC, $this->Options['RingCrossOverCuttingPoint'], strlen($Chromosome[0]));
                } else {
                    $NewChromosome[0] = substr($ChromosomeC, $this->Options['RingCrossOverCuttingPoint'], strlen($ChromosomeC));
                    $NewChromosome[0] .= substr($ChromosomeC, 0, $this->Options['RingCrossOverCuttingPoint']-strlen($Chromosome[0]));
                    $NewChromosome[1] = substr($ChromosomeC, $this->Options['RingCrossOverCuttingPoint']-strlen($Chromosome[0]), strlen($Chromosome[0]));
                }


                $this->TwoParents[0] = array(
                    'CAction'           => array(
                        'Parent 0'      => $Chromosome[0],
                        'Parent 1'      => $Chromosome[1],
                        'Type'          => 'Ring',
                        'Cutting Point' => $this->Options['RingCrossOverCuttingPoint'],
                        'NEW Parent 0'  => $NewChromosome[0],
                        'NEW Parent 1'  => $NewChromosome[1]
                    ),
                    'MAction'           => "",
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[0],
                    'Text'              => $this->BinToString($NewChromosome[0],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[0]) * 2,
                    'Age'               => 0
                );


                $this->TwoParents[1] = array(
                    'CAction'           => array(
                        'Parent 0'      => $Chromosome[0],
                        'Parent 1'      => $Chromosome[1],
                        'Type'          => 'Ring',
                        'Cutting Point' => $this->Options['RingCrossOverCuttingPoint'],
                        'NEW Parent 0'  => $NewChromosome[0],
                        'NEW Parent 1'  => $NewChromosome[1]
                    ),
                    'MAction'           => "",
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[1],
                    'Text'              => $this->BinToString($NewChromosome[1],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[1]) * 2,
                    'Age'               => 0
                );


            }
        } else {
            $this->TwoParents[0]['CAction'] = "";
            $this->TwoParents[1]['CAction'] = "";
        }
    }
    function Mutation () {
        if (rand(1,100) <= $this->Options['MutationRate']) {
            if ($this->Options['MutationTechnique'] == "1") {
                $MutationTechnique = rand(2,7);
            } else {
                $MutationTechnique = $this->Options['MutationTechnique'];
            }

            $Chromosome[0] = $this->TwoParents[0]['Chromosome'];
            $Chromosome[1] = $this->TwoParents[1]['Chromosome'];

            if ($MutationTechnique == 2)     {
                $NewChromosome[0] = str_split($Chromosome[0]);
                $NewChromosome[1] = str_split($Chromosome[1]);

                $RandomBit = rand(0,(count($NewChromosome[1])-1));

                $NewChromosome[0][$RandomBit] = ($NewChromosome[0][$RandomBit] == "1")? "0":"1";
                $NewChromosome[1][$RandomBit] = ($NewChromosome[1][$RandomBit] == "1")? "0":"1";

                $NewChromosome[0] = implode($NewChromosome[0]);
                $NewChromosome[1] = implode($NewChromosome[1]);

                $this->TwoParents[0] = array(
                    'CAction'           => $this->TwoParents[0]['CAction'],
                    'MAction'           => array(
                        'Parent 0'      => substr($Chromosome[0], 0, $RandomBit) ."[". substr($Chromosome[0], $RandomBit, 1) ."]". substr($Chromosome[0], $RandomBit+1, strlen($Chromosome[0])),
                        'Parent 1'      => substr($Chromosome[1], 0, $RandomBit) ."[". substr($Chromosome[1], $RandomBit, 1) ."]". substr($Chromosome[1], $RandomBit+1, strlen($Chromosome[1])),
                        'Type'          => 'BitFlip',
                        'Bit'           => $RandomBit,
                        'NEW Parent 0'  => substr($NewChromosome[0], 0, $RandomBit) ."[". substr($NewChromosome[0], $RandomBit, 1) ."]". substr($NewChromosome[0], $RandomBit+1, strlen($NewChromosome[0])),
                        'NEW Parent 1'  => substr($NewChromosome[1], 0, $RandomBit) ."[". substr($NewChromosome[1], $RandomBit, 1) ."]". substr($NewChromosome[1], $RandomBit+1, strlen($NewChromosome[1])),
                    ),
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[0],
                    'Text'              => $this->BinToString($NewChromosome[0],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[0]) * 2,
                    'Age'               => 0
                );

                $this->TwoParents[1] = array(
                    'CAction'           => $this->TwoParents[1]['CAction'],
                    'MAction'           => array(
                        'Parent 0'      => substr($Chromosome[0], 0, $RandomBit) ."[". substr($Chromosome[0], $RandomBit, 1) ."]". substr($Chromosome[0], $RandomBit+1, strlen($Chromosome[0])),
                        'Parent 1'      => substr($Chromosome[1], 0, $RandomBit) ."[". substr($Chromosome[1], $RandomBit, 1) ."]". substr($Chromosome[1], $RandomBit+1, strlen($Chromosome[1])),
                        'Type'          => 'BitFlip',
                        'Bit'           => $RandomBit,
                        'NEW Parent 0'  => substr($NewChromosome[0], 0, $RandomBit) ."[". substr($NewChromosome[0], $RandomBit, 1) ."]". substr($NewChromosome[0], $RandomBit+1, strlen($NewChromosome[0])),
                        'NEW Parent 1'  => substr($NewChromosome[1], 0, $RandomBit) ."[". substr($NewChromosome[1], $RandomBit, 1) ."]". substr($NewChromosome[1], $RandomBit+1, strlen($NewChromosome[1])),
                    ),
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[1],
                    'Text'              => $this->BinToString($NewChromosome[1],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[1]) * 2,
                    'Age'               => 0
                );




            }
            else if ($MutationTechnique == 3){
                $NewChromosome[0] = str_split($Chromosome[0]);
                $NewChromosome[1] = str_split($Chromosome[1]);

                $RandomBit = rand(0, (count($NewChromosome[1]) - 1));

                $NewChromosome[0][$RandomBit] = 0;
                $NewChromosome[1][$RandomBit] = 0;

                $NewChromosome[0] = implode($NewChromosome[0]);
                $NewChromosome[1] = implode($NewChromosome[1]);

                $this->TwoParents[0] = array(
                    'CAction' => $this->TwoParents[0]['CAction'],
                    'MAction' => array(
                        'Parent 0' => substr($Chromosome[0], 0, $RandomBit) . "[" . substr($Chromosome[0], $RandomBit, 1) . "]" . substr($Chromosome[0], $RandomBit + 1, strlen($Chromosome[0])),
                        'Parent 1' => substr($Chromosome[1], 0, $RandomBit) . "[" . substr($Chromosome[1], $RandomBit, 1) . "]" . substr($Chromosome[1], $RandomBit + 1, strlen($Chromosome[1])),
                        'Type' => 'Reset',
                        'Bit' => $RandomBit,
                        'NEW Parent 0' => substr($NewChromosome[0], 0, $RandomBit) . "[" . substr($NewChromosome[0], $RandomBit, 1) . "]" . substr($NewChromosome[0], $RandomBit + 1, strlen($NewChromosome[0])),
                        'NEW Parent 1' => substr($NewChromosome[1], 0, $RandomBit) . "[" . substr($NewChromosome[1], $RandomBit, 1) . "]" . substr($NewChromosome[1], $RandomBit + 1, strlen($NewChromosome[1])),
                    ),
                    'IAction' => "",
                    'Chromosome' => $NewChromosome[0],
                    'Text' => $this->BinToString($NewChromosome[0], $this->Options['CodingType']),
                    'Fitness' => strlen($NewChromosome[0]) * 2,
                    'Age' => 0
                );

                $this->TwoParents[1] = array(
                    'CAction' => $this->TwoParents[1]['CAction'],
                    'MAction' => array(
                        'Parent 0' => substr($Chromosome[0], 0, $RandomBit) . "[" . substr($Chromosome[0], $RandomBit, 1) . "]" . substr($Chromosome[0], $RandomBit + 1, strlen($Chromosome[0])),
                        'Parent 1' => substr($Chromosome[1], 0, $RandomBit) . "[" . substr($Chromosome[1], $RandomBit, 1) . "]" . substr($Chromosome[1], $RandomBit + 1, strlen($Chromosome[1])),
                        'Type' => 'Reset',
                        'Bit' => $RandomBit,
                        'NEW Parent 0' => substr($NewChromosome[0], 0, $RandomBit) . "[" . substr($NewChromosome[0], $RandomBit, 1) . "]" . substr($NewChromosome[0], $RandomBit + 1, strlen($NewChromosome[0])),
                        'NEW Parent 1' => substr($NewChromosome[1], 0, $RandomBit) . "[" . substr($NewChromosome[1], $RandomBit, 1) . "]" . substr($NewChromosome[1], $RandomBit + 1, strlen($NewChromosome[1])),
                    ),
                    'IAction' => "",
                    'Chromosome' => $NewChromosome[1],
                    'Text' => $this->BinToString($NewChromosome[1], $this->Options['CodingType']),
                    'Fitness' => strlen($NewChromosome[1]) * 2,
                    'Age' => 0
                );


            }
            else if ($MutationTechnique == 4)
            {
                $NewChromosome[0] = str_split($Chromosome[0]);
                $NewChromosome[1] = str_split($Chromosome[1]);

                $RandomBit1 = rand(0,floor(count($NewChromosome[1])/2));
                $RandomBit2 = rand(floor(count($NewChromosome[1])/2)+1,(count($NewChromosome[1])-1));

                /*
                    $a =  $a + $b;  // 5 + 6 = 11
                    $b = $a - $b;   // 11 - 6 = 5
                    $a = $a - $b;  // 11 - 5 = 6
                 */
                $NewChromosome[0][$RandomBit1] = $NewChromosome[0][$RandomBit1] + $NewChromosome[0][$RandomBit2];
                $NewChromosome[0][$RandomBit2] = $NewChromosome[0][$RandomBit1] - $NewChromosome[0][$RandomBit2];
                $NewChromosome[0][$RandomBit1] = $NewChromosome[0][$RandomBit1] - $NewChromosome[0][$RandomBit2];


                $NewChromosome[1][$RandomBit1] = $NewChromosome[1][$RandomBit1] + $NewChromosome[1][$RandomBit2];
                $NewChromosome[1][$RandomBit2] = $NewChromosome[1][$RandomBit1] - $NewChromosome[1][$RandomBit2];
                $NewChromosome[1][$RandomBit1] = $NewChromosome[1][$RandomBit1] - $NewChromosome[1][$RandomBit2];


                $NewChromosome[0] = implode($NewChromosome[0]);
                $NewChromosome[1] = implode($NewChromosome[1]);

                $this->TwoParents[0] = array(
                    'CAction'           => $this->TwoParents[0]['CAction'],
                    'MAction'           => array(
                        'Parent 0'      => substr($Chromosome[0], 0, $RandomBit1) ."[". substr($Chromosome[0], $RandomBit1, 1) ."]". substr($Chromosome[0], $RandomBit1+1, $RandomBit2)."[".substr($Chromosome[0], $RandomBit2, 1) ."]". substr($Chromosome[0], $RandomBit2+1, strlen($Chromosome[0])),
                        'Parent 1'      => substr($Chromosome[1], 0, $RandomBit1) ."[". substr($Chromosome[1], $RandomBit1, 1) ."]". substr($Chromosome[1], $RandomBit1+1, $RandomBit2)."[".substr($Chromosome[1], $RandomBit2, 1) ."]". substr($Chromosome[1], $RandomBit2+1, strlen($Chromosome[1])),
                        'Type'          => 'Swap',
                        'Bit1'          => $RandomBit1,
                        'Bit2'          => $RandomBit2,
                        'NEW Parent 0'  => substr($NewChromosome[0], 0, $RandomBit1) ."[". substr($NewChromosome[0], $RandomBit1, 1) ."]". substr($NewChromosome[0], $RandomBit1+1, $RandomBit2)."[".substr($NewChromosome[0], $RandomBit2, 1) ."]". substr($NewChromosome[0], $RandomBit2+1, strlen($NewChromosome[0])),
                        'NEW Parent 1'  => substr($NewChromosome[1], 0, $RandomBit1) ."[". substr($NewChromosome[1], $RandomBit1, 1) ."]". substr($NewChromosome[1], $RandomBit1+1, $RandomBit2)."[".substr($NewChromosome[1], $RandomBit2, 1) ."]". substr($NewChromosome[1], $RandomBit2+1, strlen($NewChromosome[1])),
                    ),
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[0],
                    'Text'              => $this->BinToString($NewChromosome[0],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[0]) * 2,
                    'Age'               => 0
                );

                $this->TwoParents[1] = array(
                    'CAction'           => $this->TwoParents[0]['CAction'],
                    'MAction'           => array(
                        'Parent 0'      => substr($Chromosome[0], 0, $RandomBit1) ."[". substr($Chromosome[0], $RandomBit1, 1) ."]". substr($Chromosome[0], $RandomBit1+1, $RandomBit2)."[".substr($Chromosome[0], $RandomBit2, 1) ."]". substr($Chromosome[0], $RandomBit2+1, strlen($Chromosome[0])),
                        'Parent 1'      => substr($Chromosome[1], 0, $RandomBit1) ."[". substr($Chromosome[1], $RandomBit1, 1) ."]". substr($Chromosome[1], $RandomBit1+1, $RandomBit2)."[".substr($Chromosome[1], $RandomBit2, 1) ."]". substr($Chromosome[1], $RandomBit2+1, strlen($Chromosome[1])),
                        'Type'          => 'Swap',
                        'Bit1'          => $RandomBit1,
                        'Bit2'          => $RandomBit2,
                        'NEW Parent 0'  => substr($NewChromosome[0], 0, $RandomBit1) ."[". substr($NewChromosome[0], $RandomBit1, 1) ."]". substr($NewChromosome[0], $RandomBit1+1, $RandomBit2)."[".substr($NewChromosome[0], $RandomBit2, 1) ."]". substr($NewChromosome[0], $RandomBit2+1, strlen($NewChromosome[0])),
                        'NEW Parent 1'  => substr($NewChromosome[1], 0, $RandomBit1) ."[". substr($NewChromosome[1], $RandomBit1, 1) ."]". substr($NewChromosome[1], $RandomBit1+1, $RandomBit2)."[".substr($NewChromosome[1], $RandomBit2, 1) ."]". substr($NewChromosome[1], $RandomBit2+1, strlen($NewChromosome[1])),
                    ),
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[1],
                    'Text'              => $this->BinToString($NewChromosome[1],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[1]) * 2,
                    'Age'               => 0
                );




            }
            else if ($MutationTechnique == 5)
            {
                $NewChromosome[0] = str_split($Chromosome[0]);
                $NewChromosome[1] = str_split($Chromosome[1]);

                $RandomBit1 = rand(0,floor(count($NewChromosome[1])/2));
                $RandomBit2 = rand(floor(count($NewChromosome[1])/2)+1,(count($NewChromosome[1])-1));

                $Scramble[0] = array();
                $Scramble[1] = array();
                for($i = $RandomBit1; $i < $RandomBit2; $i++) {
                    array_push($Scramble[0],$NewChromosome[0][$i]);
                    array_push($Scramble[1],$NewChromosome[1][$i]);
                }
                shuffle($Scramble[0]);
                shuffle($Scramble[1]);

                for($i = $RandomBit1,$j=0; $i < $RandomBit2; $i++,$j++) {
                    $NewChromosome[0][$i] = $Scramble[0][$j];
                    $NewChromosome[1][$i] = $Scramble[1][$j];
                }

                $NewChromosome[0] = implode($NewChromosome[0]);
                $NewChromosome[1] = implode($NewChromosome[1]);

                $this->TwoParents[0] = array(
                    'CAction'           => $this->TwoParents[0]['CAction'],
                    'MAction'           => array(
                        'Parent 0'      => substr($Chromosome[0], 0, $RandomBit1) ."[". substr($Chromosome[0], $RandomBit1, $RandomBit2-$RandomBit1) ."]". substr($Chromosome[0], $RandomBit2, strlen($Chromosome[0])),
                        'Parent 1'      => substr($Chromosome[1], 0, $RandomBit1) ."[". substr($Chromosome[1], $RandomBit1, $RandomBit2-$RandomBit1) ."]". substr($Chromosome[1], $RandomBit2, strlen($Chromosome[1])),
                        'Type'          => 'Scramble',
                        'Bit1'          => $RandomBit1,
                        'Bit2'          => $RandomBit2,
                        'NEW Parent 0'  => substr($NewChromosome[0], 0, $RandomBit1) ."[". substr($NewChromosome[0], $RandomBit1, $RandomBit2-$RandomBit1) ."]". substr($NewChromosome[0], $RandomBit2, strlen($NewChromosome[0])),
                        'NEW Parent 1'  => substr($NewChromosome[1], 0, $RandomBit1) ."[". substr($NewChromosome[1], $RandomBit1, $RandomBit2-$RandomBit1) ."]". substr($NewChromosome[1], $RandomBit2, strlen($NewChromosome[1]))
                    ),
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[0],
                    'Text'              => $this->BinToString($NewChromosome[0],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[0]) * 2,
                    'Age'               => 0
                );

                $this->TwoParents[1] = array(
                    'CAction'           => $this->TwoParents[0]['CAction'],
                    'MAction'           => array(
                        'Parent 0'      => substr($Chromosome[0], 0, $RandomBit1) ."[". substr($Chromosome[0], $RandomBit1, $RandomBit2-$RandomBit1) ."]". substr($Chromosome[0], $RandomBit2, strlen($Chromosome[0])),
                        'Parent 1'      => substr($Chromosome[1], 0, $RandomBit1) ."[". substr($Chromosome[1], $RandomBit1, $RandomBit2-$RandomBit1) ."]". substr($Chromosome[1], $RandomBit2, strlen($Chromosome[1])),
                        'Type'          => 'Scramble',
                        'Bit1'          => $RandomBit1,
                        'Bit2'          => $RandomBit2,
                        'NEW Parent 0'  => substr($NewChromosome[0], 0, $RandomBit1) ."[". substr($NewChromosome[0], $RandomBit1, $RandomBit2-$RandomBit1) ."]". substr($NewChromosome[0], $RandomBit2, strlen($NewChromosome[0])),
                        'NEW Parent 1'  => substr($NewChromosome[1], 0, $RandomBit1) ."[". substr($NewChromosome[1], $RandomBit1, $RandomBit2-$RandomBit1) ."]". substr($NewChromosome[1], $RandomBit2, strlen($NewChromosome[1]))
                    ),
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[1],
                    'Text'              => $this->BinToString($NewChromosome[1],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[1]) * 2,
                    'Age'               => 0
                );



            }
            else if ($MutationTechnique == 6)
            {
                $NewChromosome[0] = str_split($Chromosome[0]);
                $NewChromosome[1] = str_split($Chromosome[1]);

                $RandomBit1 = rand(0,floor(count($NewChromosome[1])/2));
                $RandomBit2 = rand(floor(count($NewChromosome[1])/2)+1,(count($NewChromosome[1])-1));

                $Scramble[0] = array();
                $Scramble[1] = array();
                for($i = $RandomBit1; $i < $RandomBit2; $i++) {
                    array_push($Scramble[0],$NewChromosome[0][$i]);
                    array_push($Scramble[1],$NewChromosome[1][$i]);
                }

                for($i = $RandomBit1,$j=$RandomBit2-$RandomBit1-1; $i < $RandomBit2; $i++,$j--) {
                    $NewChromosome[0][$i] = $Scramble[0][$j];
                    $NewChromosome[1][$i] = $Scramble[1][$j];
                }

                $NewChromosome[0] = implode($NewChromosome[0]);
                $NewChromosome[1] = implode($NewChromosome[1]);

                $this->TwoParents[0] = array(
                    'CAction'           => $this->TwoParents[0]['CAction'],
                    'MAction'           => array(
                        'Parent 0'      => substr($Chromosome[0], 0, $RandomBit1) ."[". substr($Chromosome[0], $RandomBit1, $RandomBit2-$RandomBit1) ."]". substr($Chromosome[0], $RandomBit2, strlen($Chromosome[0])),
                        'Parent 1'      => substr($Chromosome[1], 0, $RandomBit1) ."[". substr($Chromosome[1], $RandomBit1, $RandomBit2-$RandomBit1) ."]". substr($Chromosome[1], $RandomBit2, strlen($Chromosome[1])),
                        'Type'          => 'Inversion',
                        'Bit1'          => $RandomBit1,
                        'Bit2'          => $RandomBit2,
                        'NEW Parent 0'  => substr($NewChromosome[0], 0, $RandomBit1) ."[". substr($NewChromosome[0], $RandomBit1, $RandomBit2-$RandomBit1) ."]". substr($NewChromosome[0], $RandomBit2, strlen($NewChromosome[0])),
                        'NEW Parent 1'  => substr($NewChromosome[1], 0, $RandomBit1) ."[". substr($NewChromosome[1], $RandomBit1, $RandomBit2-$RandomBit1) ."]". substr($NewChromosome[1], $RandomBit2, strlen($NewChromosome[1]))
                    ),
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[0],
                    'Text'              => $this->BinToString($NewChromosome[0],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[0]) * 2,
                    'Age'               => 0
                );

                $this->TwoParents[1] = array(
                    'CAction'           => $this->TwoParents[0]['CAction'],
                    'MAction'           => array(
                        'Parent 0'      => substr($Chromosome[0], 0, $RandomBit1) ."[". substr($Chromosome[0], $RandomBit1, $RandomBit2-$RandomBit1) ."]". substr($Chromosome[0], $RandomBit2, strlen($Chromosome[0])),
                        'Parent 1'      => substr($Chromosome[1], 0, $RandomBit1) ."[". substr($Chromosome[1], $RandomBit1, $RandomBit2-$RandomBit1) ."]". substr($Chromosome[1], $RandomBit2, strlen($Chromosome[1])),
                        'Type'          => 'Inversion',
                        'Bit1'          => $RandomBit1,
                        'Bit2'          => $RandomBit2,
                        'NEW Parent 0'  => substr($NewChromosome[0], 0, $RandomBit1) ."[". substr($NewChromosome[0], $RandomBit1, $RandomBit2-$RandomBit1) ."]". substr($NewChromosome[0], $RandomBit2, strlen($NewChromosome[0])),
                        'NEW Parent 1'  => substr($NewChromosome[1], 0, $RandomBit1) ."[". substr($NewChromosome[1], $RandomBit1, $RandomBit2-$RandomBit1) ."]". substr($NewChromosome[1], $RandomBit2, strlen($NewChromosome[1]))
                    ),
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[1],
                    'Text'              => $this->BinToString($NewChromosome[1],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[1]) * 2,
                    'Age'               => 0
                );



            }
            else if ($MutationTechnique == 7)
            {
                $NewChromosome[0] = str_split($Chromosome[0]);
                $NewChromosome[1] = str_split($Chromosome[1]);

                $RandomBit = rand(0,count($NewChromosome[0])-1);

                $NewChromosome0 = $NewChromosome[0][$RandomBit];
                $NewChromosome1 = $NewChromosome[1][$RandomBit];

                unset($NewChromosome[0][$RandomBit]);
                unset($NewChromosome[1][$RandomBit]);

                array_values($NewChromosome[0]);
                array_values($NewChromosome[1]);

                $RandomBitInsert = rand(0,count($NewChromosome[0]));

                $this->array_insert($NewChromosome[0], $NewChromosome0, $RandomBitInsert);
                $this->array_insert($NewChromosome[1], $NewChromosome1, $RandomBitInsert);

                $NewChromosome[0] = implode(array_values($NewChromosome[0]));
                $NewChromosome[1] = implode(array_values($NewChromosome[1]));

                $this->TwoParents[0] = array(
                    'CAction'           => $this->TwoParents[0]['CAction'],
                    'MAction'           => array(
                        'Parent 0'      => substr($Chromosome[0], 0, $RandomBit) . "[" . substr($Chromosome[0], $RandomBit, 1) . "]" . substr($Chromosome[0], $RandomBit + 1, strlen($Chromosome[0])),
                        'Parent 1'      => substr($Chromosome[1], 0, $RandomBit) . "[" . substr($Chromosome[1], $RandomBit, 1) . "]" . substr($Chromosome[1], $RandomBit + 1, strlen($Chromosome[1])),
                        'Type'          => 'Insertion Pop',
                        'Pop'           => $RandomBit,
                        'Push'          => $RandomBitInsert,
                        'NEW Parent 0'  => substr($NewChromosome[0], 0, $RandomBitInsert) . "[" . substr($NewChromosome[0], $RandomBitInsert, 1) . "]" . substr($NewChromosome[0], $RandomBitInsert + 1, strlen($NewChromosome[0])),
                        'NEW Parent 1'  => substr($NewChromosome[1], 0, $RandomBitInsert) . "[" . substr($NewChromosome[1], $RandomBitInsert, 1) . "]" . substr($NewChromosome[1], $RandomBitInsert + 1, strlen($NewChromosome[1]))
                    ),
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[0],
                    'Text'              => $this->BinToString($NewChromosome[0],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[0]) * 2,
                    'Age'               => 0
                );

                $this->TwoParents[1] = array(
                    'CAction'           => $this->TwoParents[0]['CAction'],
                    'MAction'           => array(
                        'Parent 0'      => substr($Chromosome[0], 0, $RandomBit) . "[" . substr($Chromosome[0], $RandomBit, 1) . "]" . substr($Chromosome[0], $RandomBit + 1, strlen($Chromosome[0])),
                        'Parent 1'      => substr($Chromosome[1], 0, $RandomBit) . "[" . substr($Chromosome[1], $RandomBit, 1) . "]" . substr($Chromosome[1], $RandomBit + 1, strlen($Chromosome[1])),
                        'Type'          => 'Insertion Pop',
                        'Pop'           => $RandomBit,
                        'Push'          => $RandomBitInsert,
                        'NEW Parent 0'  => substr($NewChromosome[0], 0, $RandomBitInsert) . "[" . substr($NewChromosome[0], $RandomBitInsert, 1) . "]" . substr($NewChromosome[0], $RandomBitInsert + 1, strlen($NewChromosome[0])),
                        'NEW Parent 1'  => substr($NewChromosome[1], 0, $RandomBitInsert) . "[" . substr($NewChromosome[1], $RandomBitInsert, 1) . "]" . substr($NewChromosome[1], $RandomBitInsert + 1, strlen($NewChromosome[1]))
                    ),
                    'IAction'           => "",
                    'Chromosome'        => $NewChromosome[1],
                    'Text'              => $this->BinToString($NewChromosome[1],$this->Options['CodingType']),
                    'Fitness'           => strlen($NewChromosome[1]) * 2,
                    'Age'               => 0
                );



            }
        } else {
            $this->TwoParents[0]['MAction'] = "";
            $this->TwoParents[1]['MAction'] = "";
        }
    }
    function Inversion() {
        $Chromosome[0] = $this->TwoParents[0]['Chromosome'];
        $Chromosome[1] = $this->TwoParents[1]['Chromosome'];

        if (rand(1,100) <= $this->Options['InversionRate']) {
            $NewChromosome[0] = strrev($Chromosome[0]);
            $this->TwoParents[0] = array(
                'CAction'           => $this->TwoParents[0]['CAction'],
                'MAction'           => $this->TwoParents[0]['MAction'],
                'IAction'           => array(
                    'Parent 0'      => $Chromosome[0],
                    'Parent 1'      => $Chromosome[1],
                    'Type'          => 'Inversion Parent 0',
                    'NEW Parent 0'  => $NewChromosome[0]
                ),
                'Chromosome'        => $NewChromosome[0],
                'Text'              => $this->BinToString($NewChromosome[0],$this->Options['CodingType']),
                'Fitness'           => strlen($NewChromosome[0]) * 2,
                'Age'               => 0
            );
        } else {
            $this->TwoParents[0]['IAction'] = "";
        }


        if (rand(1,100) <= $this->Options['InversionRate']) {
            $NewChromosome[1] = strrev($Chromosome[1]);
            $this->TwoParents[1] = array(
                'CAction'           => $this->TwoParents[1]['CAction'],
                'MAction'           => $this->TwoParents[1]['MAction'],
                'IAction'           => array(
                    'Parent 0'      => $Chromosome[0],
                    'Parent 1'      => $Chromosome[1],
                    'Type'          => 'Inversion Parent 1',
                    'NEW Parent 1'  => $NewChromosome[1]
                ),
                'Chromosome'        => $NewChromosome[1],
                'Text'              => $this->BinToString($NewChromosome[1],$this->Options['CodingType']),
                'Fitness'           => strlen($NewChromosome[1]) * 2,
                'Age'               => 0
            );
        } else {
            $this->TwoParents[1]['IAction'] = "";
        }
    }


    function Run() {
        do {
            $this->Generate_New_Generation();
            $this->Evaluation();
        } while (
            (count($this->Generations) <= $this->Options['MaxIteration'])
            &&
            (isset($this->Population[0]['Fitness']) && ($this->Population[0]['Fitness'] != $this->Options['MinAcceptableFitness']) )
        );
    }








    function array_insert(&$array, $insert, $position)
    {
        if (is_int($position)) {
            array_splice($array, $position, 0, $insert);
        } else {
            $pos   = array_search($position, array_keys($array));
            $array = array_merge(
                array_slice($array, 0, $pos),
                $insert,
                array_slice($array, $pos)
            );
        }
    }
    function array_order_by() {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }
    function CharToBin($Char,$CodedType='Binary') {
        if ($CodedType == "Gray") {
            return $this->Gray_Encode(ord($Char),true,7);
        }
        return decbin(ord($Char));
    }
    function BinToChar($Bin,$CodedType='Binary') {
        if ($CodedType == "Gray") {
            $GrayBin = bindec($Bin);
            $Bin = $this->Gray_Decode($GrayBin);
            return chr($Bin);
        }
        return chr(bindec($Bin));
    }
    function BinToString($Bin,$CodedType='Binary',$CharLength = 7) {
        $String = "";
        for($i = 0; $i < strlen($Bin); $i+=7) {
            $Char = $this->BinToChar(substr($Bin, $i, $CharLength),$CodedType);
            if (array_key_exists($Char,$this->CharTable))
                $String .= $Char;
            else
                $String .= "?";
        }

        return $String;
    }

    function Gray_Encode($Binary,$returnbin=false,$Length=false) {
        $Gray = $Binary ^ ($Binary >> 1);
        if ($returnbin) {
            $Gray = decbin ($Gray);
            if ($Length) {
                $Gray = str_pad($Gray, $Length, '0', STR_PAD_LEFT);
            }
        }
        return $Gray;
    }
    function Gray_Decode($Gray){
        $binary = $Gray;
        while($Gray >>= 1) $binary ^= $Gray;
        return $binary;
    }
}