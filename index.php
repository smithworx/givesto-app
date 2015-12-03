<html ng-app="app">

<head>
  <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.3.10/angular.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
  <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="style.css" media="screen" />
</head>

<body>
  <?php @include_once("../../analyticstracking.php") ?>
    <header>
      <h1><i class="fa fa-lg fa-fw fa-gift"></i> Gift Giving Tool
        <span class="tagline">Quickly generate gift-giving lists fairly and squarely</span>
      </h1>
    </header>


    <div id="main" ng-controller="InputController">


      <div class="form-group editor-wrapper">
        <h2 style="display: inline-block">Groups</h2>
        <span class="description">( enter grouped lists of people... just like the example )</span>
        <textarea class="editor" ng-change="go()" id="user_input" ng-class="{'unparseable': !parseable}" ng-model="user_input" rows="5" style="width: 100%;" placeholder="">
        </textarea>
      </div>


      <div id="results" ng-if="finished_input.length">
        <h2 style="display: inline-block">Giving List</h2>

        <!-- Nav tabs -->
        ( Style:
        <a href="#r-text" onclick="return false;" aria-controls="text" role="tab" data-toggle="tab">Text</a> | <a href="#r-table" onclick="return false;" aria-controls="table" role="tab" data-toggle="tab">Table</a> | <a href="#r-seqdia" onclick="return false;"
        aria-controls="seqdia" role="tab" data-toggle="tab">Seqdia</a> )

        <!-- Tab panes -->
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="r-text">

            <div ng-repeat="(key,value) in result">
              <b>{{key}}</b> gives to <b>{{value}}</b>
            </div>

          </div>
          <div role="tabpanel" class="tab-pane" id="r-table">

            <table>
              <tr>
                <th>Giver</th>
                <th>Recipient</th>
              </tr>
              <tr ng-repeat="(key,value) in result">
                <td>{{key}}</td>
                <td>{{value}}</td>
              </tr>
            </table>

          </div>
          <div role="tabpanel" class="tab-pane" id="r-seqdia">

            <div ng-repeat="(key,value) in result">
              <b>{{key}}</b>--><b>{{value}}</b>: gives to
            </div>

          </div>
        </div>


      </div>



      <footer>
        <span ng-hide="!parseable">
          <button class="btn btn-link" ng-click="go()" title="Randomize List"><i class="fa fa-lg fa-refresh"></i> </button>
          <button class="btn btn-link" ng-click="download()" title="Download as CSV"><i class="fa fa-lg fa-download"></i> </button>
          <span style="font-size: .8em">Gift Giving List Randomly Generated: {{timestamp | date:'M/d/yy h:mm:ss a'}}</span>
        </span>


        <span class="credit"><a href="http://m.smithworx.com"><i class="fa fa-heart fa-lg fa-fw"></i>Matt Smith</a> <a href="http://github.com/smithworx/givesto-app"><i class="fa fa-lg fa-fw fa-github-square" style="color: #64E986; margin-left: 20px; font-size: 1.8em;"></i></a></span>
      </footer>

    </div>


    <script type="text/javascript">
      var myApp = angular.module('app', []);

      function sortObject(o) {
        var sorted = {},
          key, a = [];
        for (key in o) {
          if (o.hasOwnProperty(key)) {
            a.push(key);
          }
        }
        a.sort();
        for (key = 0; key < a.length; key++) {
          sorted[a[key]] = o[a[key]];
        }
        return sorted;
      }

      myApp.controller('InputController', function InputController($scope, $http, $location) {
        $scope.finished_input = [];
        $scope.submitting = false;
        $scope.parseable = true;
        $scope.canned_input = 'John, Paul, George, Ringo\nElmo, Oscar, Big Bird, Bert\nLarry, Curly, Moe';
        //$scope.canned_input = '["John","Paul","George","Ringo"],\n["Elmo","Oscar","Big Bird","Bert"],\n["Larry","Curly","Moe"]';
        $scope.user_input = $scope.canned_input;
        $scope.result = {};

        $scope.go = function() {
          if ($scope.user_input) {

            $scope.submitting = true;
            $http.get('api.php', {
              params: {
                'input': $scope.user_input
              },
              cache: false
            }).
            success(function(data, status, headers, config) {
              // this callback will be called asynchronously
              // when the response is available
              if ($scope.canned_input !== $scope.user_input) {
                //console.log("change path: "+btoa($scope.user_input));
                $location.path(btoa($scope.user_input));
              }
              $scope.timestamp = new Date();
              $scope.result = data;
              $scope.finished_input.push(data);
              $scope.submitting = false;
              $scope.parseable = true;
              $('#user_input').focus();
            }).
            error(function(data, status, headers, config) {
              // called asynchronously if an error occurs
              // or server returns response with an error status.
              //console.log("ERROR");
              $scope.submitting = false;
              $scope.parseable = false;
            });
            //$scope.user_input = '';
          } else {
            alert('Please enter valid input to retrieve results');
            $('#user_input').focus();
          }
        };

        if ($location.path().substr(1).length) {
          $scope.user_input = atob($location.path().substr(1));
        }
        $scope.go();



        //Allows the user to download a .csv file
        $scope.download = function() {
          var download_data = sortObject($scope.result);
          var csvContent = "data:text/csv;charset=utf-8," + "GIVER, " + "RECIPIENT" + "\n";

          for (var i in download_data) {
            csvContent += i + ", " + download_data[i] + "\n";
          }

          var encodedUri = encodeURI(csvContent);
          var link = document.createElement("a");
          link.setAttribute("href", encodedUri);
          link.setAttribute("download", "giving-list.csv");
          link.click();
        }
      });
    </script>

</body>

</html>
