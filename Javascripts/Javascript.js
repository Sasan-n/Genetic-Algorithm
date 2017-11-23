$('#CrossOverRate, #MutationRate, #InversionRate, #UniformCrossOverRatio').change(function (e) {
    if ($(this).val() < 0) {
        $(this).val('0');
    } else if ($(this).val() > 1) {
        $(this).val('1');
    }
});

$('#CrossOverTechnique').change(function (e) {
    $('#AllCrossOverTechniqueConfigurations > div').addClass('hidden');
    $('#Crossover_T'+$(this).val()+'').removeClass('hidden');
});

$('#MutationTechnique').change(function (e) {
    $('#AllMutationTechniqueConfigurations > div').addClass('hidden');
    $('#Mutation_T'+$(this).val()+'').removeClass('hidden');
});

$('#RingCrossOverCuttingByRandom').change(function (e) {
    $('#RingCrossOverCuttingPoint').val('');
});

$('.btn-toggle').click(function() {
    $(this).find('.btn').toggleClass('active');

    if ($(this).find('.on').hasClass('btn-success') > 0) {
        $(this).find('.on').addClass('btn-white').removeClass('btn-success');
    } else {
        $(this).find('.on').removeClass('btn-white').addClass('btn-success');
    }
    if ($(this).find('.off').hasClass('btn-white') > 0) {
        $(this).find('.off').addClass('btn-danger').removeClass('btn-white');
    } else {
        $(this).find('.off').removeClass('btn-danger').addClass('btn-white');
    }
});


$('#GeneticForm').submit(function (e) {
    e.preventDefault();

    $.ajax({
        type: 'POST',
        url: 'Ajax.php',
        data: 'Action=RunGeneticAlgorithm&'+$(this).serialize()+'&RingCrossOverCuttingByRandom='+$('#RingCrossOverCuttingByRandom').prop('checked')+'&Elitism='+$('#Elitism .on').hasClass('active')+'' ,
        encoding: 'UTF-8',
        beforeSend : function() {
            $('.GeneticResult').html('<div class="Html5_02_Loading_Container"><div class="Loading"></div></div>');
        },
        success :  function( msg ) {
            $('.GeneticResult').html(msg);
        },
        error :  function( msg ) {

        }
    });
});









