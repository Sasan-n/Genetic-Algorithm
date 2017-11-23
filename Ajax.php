<?php
set_time_limit (0);
ini_set('memory_limit', '1024M'); // or you could use 1G



require_once 'Genetic.class.php';


if (isset($_POST['Action']) && $_POST['Action'] == "RunGeneticAlgorithm") {

    if ($_POST['CrossOverTechnique'] == "1") {
        $_POST['SingleCrossOverPoint'] = false;
        $_POST['TwoCrossOverPoint1'] = false;
        $_POST['TwoCrossOverPoint2'] = false;
        $_POST['UniformCrossOverRatio'] = false;
        $_POST['RingCrossOverCuttingPoint'] = false;
        $_POST['RingCrossOverCuttingByRandom'] = true;
    }
    $Genetic = new GeneticAlgorithm (
        array(
            'Goal' => $_POST['Goal'],

            'PopulationSize' => $_POST['PopulationSize'],
            'MaxIteration' => $_POST['MaxIteration'],
            'GenerationGap' => $_POST['GenerationGap']*100,
            'Age' => $_POST['Age'],
            'Strategy' => $_POST['Strategy'],
            'MaxPopulationSizeM+L' => '100',
            'NormPopulationSizeM+L' => '2',
            'Elitism' => $_POST['Elitism'],

            'MinAcceptableFitness' => $_POST['MinFitness'],
            'CodingType' => $_POST['CodingType'],

            'CrossOverRate' => $_POST['CrossOverRate']*100,
            'MutationRate' => $_POST['MutationRate']*100,
            'InversionRate' => $_POST['InversionRate']*100,

            'CrossOverTechnique' => $_POST['CrossOverTechnique'],
            'MutationTechnique' => $_POST['MutationTechnique'],

            'SingleCrossOverPoint' => $_POST['SingleCrossOverPoint'],
            'TwoCrossOverPoint1' => $_POST['TwoCrossOverPoint1'],
            'TwoCrossOverPoint2' => $_POST['TwoCrossOverPoint2'],
            'UniformCrossOverRatio' => $_POST['UniformCrossOverRatio']*100,
            'RingCrossOverCuttingPoint' => $_POST['RingCrossOverCuttingPoint'],
            'RingCrossOverCuttingByRandom' => $_POST['RingCrossOverCuttingByRandom']
        )
    );

    $Genetic->Run();
?>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-8 bg-white rounded text-center">
            <h3>Goal : <?php echo chunk_split($Genetic->Options['Goal'], 7, ' '); ?></h3>
        </div>
        <div class="col-2"></div>
    </div>
    <div class="row mt-2">
        <div class="col-2"></div>
        <?php
        if ($Genetic->Generations[count($Genetic->Generations)-1][0]['Fitness'] == $_POST['MinFitness']) {
            $Found = true;
            echo '<div class="col-8 bg-success rounded text-center text-white"><h1>Goal Found Generation '.(count($Genetic->Generations)-1).'</h1></div>';
        } else {
            echo '<div class="col-8 bg-danger rounded text-center text-white"><h1>Goal NotFound</h1></div>';
        }
        ?>
        <div class="col-2"></div>
    </div>

    <?php
    if ($Found) {
    ?>
        <div class="row mt-4">
            <div class="col-4"></div>
            <div class="col-4 bg-warning rounded text-center pt-1 ShowGenerationTable"><h4>Show Generation Table</h4></div>
            <div class="col-4"></div>
        </div>



        <div class="GenerationTable">
            <div class="Exit" onclick="$('.GenerationTable').hide();"><div class="close">&times;</div></div>

            <div class="DetailedBoxBody">
            <?php
            $Generations = array_reverse($Genetic->Generations, true);
            foreach ($Generations as $Generation => $Genes) {
                foreach ($Genes as $ID => $Gene) {
            ?>
                <div class="DetailedBox" id="G<?php echo $Generation ?>_N<?php echo $ID ?>">
                    <div class="text-center">
                        <h3>
                            <?php echo chunk_split($Genetic->Options['Goal'], 7, ' '); ?>
                            <br>
                            <?php echo chunk_split($Gene['Chromosome'], 7, ' '); ?>
                        </h3>
                    </div>
                    <?php
                    echo "<pre>";
                    $Print = str_replace("Array","", print_r($Gene,true));
                    echo $Print;
                    echo "</pre>";
                    ?>
                </div>
            <?php
                }
            }
            ?>
            </div>


            <?php
            $Generations = array_reverse($Genetic->Generations, true);
            foreach ($Generations as $Generation => $Genes) {
            ?>
                <div class="Generation">
                    <div class="Title">Generation <?php echo $Generation; ?></div>
                    <?php
                    echo "<div class='AllGenes clearfix'>";
                    foreach ($Genes as $ID => $Gene) {
                    ?>
                        <div class="Gene" onclick="$('.DetailedBoxBody, <?php echo "#G".$Generation."_N".$ID.""; ?>').show();"><?php echo $Gene['Fitness']; ?></div>
                    <?php
                    }
                    echo "<div class='clearfix'></div>";
                    echo "</div>";
                    ?>
                </div>
            <?php
            }
            ?>
        </div>
    <?php
    }
    ?>
    <script>
        $('.genetic .DetailedBoxBody').click(function (e) {
            $(this).hide();
            $(this).children('div').hide();
        });
        $('.genetic .ShowGenerationTable').click(function (e) {
            $('.GenerationTable').show();
        });
    </script>
<?php
}