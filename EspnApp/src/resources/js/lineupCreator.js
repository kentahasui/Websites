// Wait until the document has finished loading
$(document).ready(function() {
    /************************
     * BEGIN CLASS DECLARATIONS
     ************************/
    /************************
     * Player Class
     * Represents a Fantasy Football player
     * Has Methods to return stuff
     * @param playerData an object with the 'player-name' and 'player-stats' properties
     * @constructor
     ***********************/
    // Constructor with instance variables
    var Player = function(playerData) {
        // instance variables
        this.name = playerData['player-name'];
        this.playerStats = playerData['player-stats'] || {};
    };

    // Check if variable is a number
    Player.prototype.isNumber = function(variable) {
        return isFinite(variable) && !isNaN(variable);
    };
    // General function for returning a touchdown type.
    // Negative touchdown numbers and non-numbers are casted to 0
    Player.prototype.getTouchDowns = function(playType){
        var keyName = playType + '-touchdowns';
        var touchDowns = this.playerStats[keyName] || 0;
        if (this.isNumber(touchDowns)){
            if (touchDowns >= 0) {
                return touchDowns;
            }
        }
        return 0;
    };
    // General function for returning yards.
    // Negative non-numbers are casted to 0
    Player.prototype.getYards = function (playType){
        var keyName = playType + '-yards';
        var yards = this.playerStats[keyName] || 0;
        if (this.isNumber(yards)){
            return yards;
        }
        return 0;
    };

    // Getters for the name, position, and various numerical statistics
    Player.prototype.getName = function() {
        return this.name || '';
    };
    Player.prototype.getPosition = function() {
        return this.playerStats['position'] || '';
    };
    Player.prototype.getPassingTouchdowns = function() {
        return this.getTouchDowns('passing');
    };
    Player.prototype.getRushingTouchdowns = function() {
        return this.getTouchDowns('rushing');
    };
    Player.prototype.getReceivingTouchdowns = function() {
        return this.getTouchDowns('receiving');
    };
    Player.prototype.getPassingYards = function() {
        return this.getYards('passing');
    };
    Player.prototype.getRushingYards = function() {
        return this.getYards('rushing');
    };
    Player.prototype.getReceivingYards = function() {
        return this.getYards('receiving');
    };

    // Methods to calculate and get player score
    Player.prototype.calculateScore = function() {
        var tempScore = 0;
        tempScore += this.getPassingTouchdowns() * 4;
        tempScore += this.getReceivingTouchdowns() * 6;
        tempScore += this.getRushingTouchdowns() * 6;
        tempScore += Math.floor(this.getPassingYards() / 25);
        tempScore += Math.floor(this.getRushingYards() / 10);
        tempScore += Math.floor(this.getReceivingYards() / 10);
        return tempScore;
    };
    Player.prototype.getScore = function() {
        return this.calculateScore();
    };

    // toString() method, to easily display user stats
    Player.prototype.toString = function() {
        return this.getName() + '=' + this.getScore();
    };

    /***********************
     * BestRoster Class
     * @param playersArray  - An Array composed of Player objects
     * @constructor
     ***********************/
    var BestRoster = function(playersArray) {
        this.playersArray = playersArray;
        // Initialized roster of dummy players
        this.roster = {
            'QB': new Player({
                'player-name': 'Dummy',
                'player-stats': {}
            }),
            'RB1': new Player({
                'player-name': 'Dummy',
                'player-stats': {}
            }),
            'RB2': new Player({
                'player-name': 'Dummy',
                'player-stats': {}
            }),
            'WR1': new Player({
                'player-name': 'Dummy',
                'player-stats': {}
            }),
            'WR2': new Player({
                'player-name': 'Dummy',
                'player-stats': {}
            }),
            'TE': new Player({
                'player-name': 'Dummy',
                'player-stats': {}
            })
        };
    };

    // Methods to place RB or WR in roster.
    // Needs special processing instructions, because a roster has two RBs and two WRs
    BestRoster.prototype.processMultiPosition = function(playerObject, roster) {
        var position1 = playerObject.getPosition() + '1';
        var position2 = playerObject.getPosition() + '2';
        if (playerObject.getScore() >= roster[position2].getScore()) {
            if (playerObject.getScore() >= roster[position1].getScore()) {
                roster[position2] = roster[position1];
                roster[position1] = playerObject;
            } else {
                roster[position2] = playerObject;
            }
        }
    };

    // Method to place QB or TE in roster. Only one player from this position will be
    // placed in the roster
    BestRoster.prototype.processSinglePosition = function(playerObject, roster) {
        var position = playerObject.getPosition();
        if (playerObject.getScore() >= roster[position].getScore()) {
            roster[position] = playerObject;
        }
    };

    // Method to construct the roster from the parameters passed in
    BestRoster.prototype.constructRoster = function() {
        var index, playerObject;
        for (index = 0; index < this.playersArray.length; index++) {
            playerObject = this.playersArray[index];
            switch (playerObject.getPosition()) {
                case 'QB':
                case 'TE':
                    this.processSinglePosition(playerObject, this.roster);
                    break;
                case 'RB':
                case 'WR':
                    this.processMultiPosition(playerObject, this.roster);
                    break;
                default:
                    break;
            }
        }
    };

    // Method to accumulate the scores of the players in the roster
    BestRoster.prototype.calculateTotalScore = function(roster) {
        return Object.keys(roster).reduce(function(previous, key) {
            return previous + roster[key].getScore();
        }, 0);
    };

    // toString Method to be used in producing output
    BestRoster.prototype.toString = function() {
        var output, numberedPosition, playerObject;
        output = 'Optimal Roster: ';
        for (numberedPosition in this.roster) {
            if (this.roster.hasOwnProperty(numberedPosition)) {
                playerObject = this.roster[numberedPosition];
                output = output + numberedPosition + "-" + playerObject.toString() + ", ";
            }
        }
        output = output + 'Total=' + this.calculateTotalScore(this.roster);
        return output;
    };

    /************************
     * FantasyRosterCreator Class
     * Creates
     * @param jsonData JSON player data containing players, names and stats
     * @constructor
     *************************/
    var FantasyRosterCreator = function(jsonData) {
        this.jsonData = jsonData;
        this.bestRoster = {};
        this.playersArray = [];
    };

    // Returns an array of Player objects from well-formed JSON data
    FantasyRosterCreator.prototype.loadPlayerData = function() {
        var jsonPlayersArray = this.jsonData['players'];
        for (var index = 0; index < jsonPlayersArray.length; index++) {
            var jsonPlayerData = jsonPlayersArray[index].player;
            var playerObject = new Player(jsonPlayerData);
            this.playersArray.push(playerObject);
        }
    };

    // Creates and returns the best roster possible from the JSON data
    FantasyRosterCreator.prototype.getBestRoster = function() {
        this.bestRoster = new BestRoster(this.playersArray);
        this.bestRoster.constructRoster();
        return this.bestRoster.toString();
    };

    /***********************
     * END CLASS DECLARATIONS
     * BEGIN MAIN THREAD OF EXECUTION
     **********************/
    // Shared variables
    // Encapsulate the list of all players as well as the optimal roster
    var gRosterCreator = {};
    // Used as input to gRosterCreator
    var gJsonData = '';

    // Parses the JSON stored in jsonData.
    // Populates and returns an array of Player objects.
    // Each player can access their own name, stats, and score
    var loadPlayerJSON = function() {
        gRosterCreator = new FantasyRosterCreator(gJsonData);
        gRosterCreator.loadPlayerData();
        return gRosterCreator.playersArray;
    };

    // Finds the optimal line-up of the best QB, RB1, RB2, WR1, WR2, and TE.
    // Returns the line-up as a string
    var bestRoster = function() {
        return gRosterCreator.getBestRoster();
    };

    // Declare main function
    var main = function() {
        var url = $("#jsonURL").val();
        // First retrieves the JSON at the specified URL
        // Then parses and processes the data
        // Finally returns the best roster, given the JSON input
        $.getJSON(url, (function(data) {
                gJsonData = data;
                loadPlayerJSON();
                var rosterString = bestRoster();
                $("#roster").val(rosterString);
            }))
            .fail(function(data, textStatus, error) {
                var errMessage = "Error while retrieving JSON data. Please check the URL. ";
                errMessage += "Status: " + textStatus + ", Error: " + error;
                console.log(errMessage);
                alert(errMessage);
            });
    };

    // Bind main function to button
    $("#getRosterButton").click(main);

});