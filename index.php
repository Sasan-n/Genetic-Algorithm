<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Genetic Algorithm</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="/Styles/Normalize.css">
    <link rel="stylesheet" href="/Styles/Bootstrap.css">
    <link rel="stylesheet" href="/Styles/Style.css">
</head>


<body>
    <header class="header" id="top">
        <div class="text-vertical-center">
            <h1>Genetic Algorithm</h1>
            <br>
            <a href="#genetic" class="btn btn-outline-white btn-lg">Find Out More</a>
        </div>
    </header>

    <section id="genetic" class="genetic">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-lg-12"><h1>Execute Genetic Algorithm Based On Initialization</h1></div>
            </div>
            <div class="row">
                <div class="col-2 vcenter"><h3>Initialization</h3></div>
                <div class="col-10 pt-3 bg-info rounded text-white">
                    <form action="" id="GeneticForm">
                        <div class="row">
                            <div class="col-2">Goal String :</div>
                            <div class="col-10"><input type="text" autocomplete="false" class="form-control" name="Goal" id="Goal" value="Ax"></div>
                        </div>

                        <div class="row pt-5">
                            <div class="col-2">Crossover Rate :</div>
                            <div class="col-2"><input type="text" autocomplete="false" class="form-control" name="CrossOverRate" id="CrossOverRate" value="0.10"></div>
                            <div class="col-2">Mutation Rate :</div>
                            <div class="col-2"><input type="text" autocomplete="false" class="form-control" name="MutationRate" id="MutationRate" value="0.60"></div>
                            <div class="col-2">Inversion Rate :</div>
                            <div class="col-2"><input type="text" autocomplete="false" class="form-control" name="InversionRate" id="InversionRate" value="0.60"></div>
                        </div>

                        <div class="row pt-3">
                            <div class="col-2">Crossover Technique :</div>
                            <div class="col-2">
                                <select class="form-control" name="CrossOverTechnique" id="CrossOverTechnique">
                                    <option value="0">Random</option>
                                    <option value="1" selected>Random for each Generation</option>
                                    <option value="2">Single Point Crossover</option>
                                    <option value="3">Two Points Crossover</option>
                                    <option value="4">Uniform Crossover</option>
                                    <option value="5">Ring Crossover</option>
                                </select>
                            </div>
                            <div class="col-8" id="AllCrossOverTechniqueConfigurations">
                                <div class="row" id="Crossover_T0">
                                    <div class="col-12">Select Random From All Techniques With Default Configurations.</div>
                                </div>
                                <div class="row hidden" id="Crossover_T1">
                                    <div class="col-12">Select Random From All Techniques For Each Generation.</div>
                                </div>
                                <div class="row hidden" id="Crossover_T2">
                                    <div class="col-3">Crossover Point :</div>
                                    <div class="col-3"><input type="text" autocomplete="false" class="form-control" name="SingleCrossOverPoint" id="SingleCrossOverPoint" value="3"></div>
                                </div>
                                <div class="row hidden" id="Crossover_T3">
                                    <div class="col-3 text-nowrap">Crossover Point1 :</div>
                                    <div class="col-3"><input type="text" autocomplete="false" class="form-control" name="TwoCrossOverPoint1" id="TwoCrossOverPoint1" value="2"></div>
                                    <div class="col-3 text-nowrap">Crossover Point2 :</div>
                                    <div class="col-3"><input type="text" autocomplete="false" class="form-control" name="TwoCrossOverPoint2" id="TwoCrossOverPoint2" value="6"></div>
                                </div>
                                <div class="row hidden" id="Crossover_T4">
                                    <div class="col-3">Mixing Ratio :</div>
                                    <div class="col-3"><input type="text" autocomplete="false" class="form-control" name="UniformCrossOverRatio" id="UniformCrossOverRatio" value="0.5"></div>
                                </div>
                                <div class="row hidden" id="Crossover_T5">
                                    <div class="col-3">Cutting Point :</div>
                                    <div class="col-3"><input type="text" autocomplete="false" class="form-control" name="RingCrossOverCuttingPoint" id="RingCrossOverCuttingPoint" value="4"></div>
                                    <div class="col-6">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="checkbox" name="RingCrossOverCuttingByRandom" id="RingCrossOverCuttingByRandom"> Or Select By Random
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row pt-3">
                            <div class="col-2">Mutation Technique :</div>
                            <div class="col-2">
                                <select class="form-control" name="MutationTechnique" id="MutationTechnique">
                                    <option value="0">Random</option>
                                    <option value="1" selected>Random for each Generation</option>
                                    <option value="2">Bit Flip Mutation</option>
                                    <option value="3">Random Resetting</option>
                                    <option value="4">Swap Mutation</option>
                                    <option value="5">Scramble Mutation</option>
                                    <option value="6">Inversion Mutation</option>
                                    <option value="7">Insertion Mutation</option>
                                </select>
                            </div>
                            <div class="col-8" id="AllMutationTechniqueConfigurations">
                                <div class="row" id="Mutation_T0">
                                    <div class="col-12">Select Random From All Techniques With Default Configurations.</div>
                                </div>
                                <div class="row hidden" id="Mutation_T1">
                                    <div class="col-12">Select Random From All Techniques For Each Generation.</div>
                                </div>
                                <div class="row hidden" id="Mutation_T2">
                                    <div class="col-12">Select One Random Bit and Flip It.</div>
                                </div>
                                <div class="row hidden" id="Mutation_T3">
                                    <div class="col-12">Select One Random Bit and Reset It.</div>
                                </div>
                                <div class="row hidden" id="Mutation_T4">
                                    <div class="col-12">Interchange Two Random Bit of Chromosome</div>
                                </div>
                                <div class="row hidden" id="Mutation_T5">
                                    <div class="col-12">A Subset of Genes Is Chosen and Their Values are Shuffled Randomly</div>
                                </div>
                                <div class="row hidden" id="Mutation_T6">
                                    <div class="col-12">A Subset of Genes Is Chosen and Their Values Inverted.</div>
                                </div>
                                <div class="row hidden" id="Mutation_T7">
                                    <div class="col-12">A One From Genes Get Out and Reinsert Randomly.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row pt-5">
                            <div class="col-2">Population Size :</div>
                            <div class="col-2"><input type="text" autocomplete="false" class="form-control" name="PopulationSize" id="PopulationSize" value="100"></div>
                            <div class="col-2">Generation Gap :</div>
                            <div class="col-2"><input type="text" autocomplete="false" class="form-control" name="GenerationGap" id="GenerationGap" value="1"></div>
                            <div class="col-2">Coding Type :</div>
                            <div class="col-2">
                                <select class="form-control" name="CodingType" id="CodingType">
                                    <option value="Binary" selected>Binary Coded</option>
                                    <option value="Gray">Gray Coded</option>
                                </select>
                            </div>
                        </div>

                        <div class="row pt-3">
                            <div class="col-2">Elitism :</div>
                            <div class="col-2">
                                <div class="btn-group btn-toggle" id="Elitism">
                                    <button type="button" class="btn on btn-success active">ON</button>
                                    <button type="button" class="btn off btn-white">OFF</button>
                                </div>
                            </div>
                            <div class="col-2">Age :</div>
                            <div class="col-2"><input type="text" autocomplete="false" class="form-control" name="Age" id="Age" value="10"></div>
                            <div class="col-2">Strategy :</div>
                            <div class="col-2">
                                <select class="form-control" name="Strategy" id="Strategy">
                                    <option value="M,L" selected>M,L</option>
                                    <option value="M+L">M+L</option>
                                </select>
                            </div>
                        </div>

                        <div class="row pt-5">
                            <div class="col-2">Max Iteration :</div>
                            <div class="col-2"><input type="text" autocomplete="false" class="form-control" name="MaxIteration" id="MaxIteration" value="300"></div>
                            <div class="col-2">Min Fitness :</div>
                            <div class="col-2"><input type="text" autocomplete="false" class="form-control" name="MinFitness" id="MinFitness" value="0"></div>
                            <div class="col-2"></div>
                            <div class="col-2"></div>
                        </div>

                        <div class="row pt-3 pb-3">
                            <div class="col-4"></div>
                            <div class="col-4 text-center">
                                <button type="submit" class="btn btn-primary btn-lg" id="RunGenetic">Run Algorithm</button>
                            </div>
                            <div class="col-4"></div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-2 text-center"><h3>Result</h3></div>
                <div class="col-10 pt-3 pb-3 bg-dark rounded GeneticResult"></div>
            </div>
        </div>
    </section>


    <footer id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto text-center">
                    <h4>
                        <a href="https://sasannobakht.com/"><strong>Sasan Nobakht</strong></a>
                    </h4>
                    <p>Khayyam University
                        <br>ITSC Department</p>
                    <ul class="list-unstyled">
                        <li>
                            <i class="fa fa-phone fa-fw"></i>
                            (+98) 936-418-4462</li>
                        <li>
                            <i class="fa fa-envelope-o fa-fw"></i>
                            <a href="mailto:me@sasannobakht.com">me@sasannobakht.com</a>
                        </li>
                    </ul>
                    <br>
                    <ul class="list-inline">
                        <li class="list-inline-item">
                            <a href="https://www.facebook.com/Sasannobakht">
                                <i class="fa fa-facebook fa-fw fa-3x"></i>
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a href="https://twitter.com/SasanNobakht">
                                <i class="fa fa-twitter fa-fw fa-3x"></i>
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a href="https://www.instagram.com/___sasan___">
                                <i class="fa fa-instagram fa-fw fa-3x"></i>
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a href="https://linkedin.com/in/sasannobakht">
                                <i class="fa fa-linkedin fa-fw fa-3x"></i>
                            </a>
                        </li>
                    </ul>
                    <hr class="small">
                    <p class="text-muted">Copyright &copy; 2017</p>
                </div>
            </div>
        </div>
    </footer>
</body>


<script type="text/javascript" src="/Javascripts/JQuery.js"></script>
<script type="text/javascript" src="/Javascripts/Popper.js"></script>
<script type="text/javascript" src="/Javascripts/Bootstrap.js"></script>
<script type="text/javascript" src="/Javascripts/Bootstrap.Bundle.js"></script>
<script type="text/javascript" src="/Javascripts/JQuery.Easing.js"></script>
<script type="text/javascript" src="/Javascripts/Javascript.js"></script>
</html>