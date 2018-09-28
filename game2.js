//Javascript Refactor for Island Rush (From K2's file)
//Spencer Adolph 09/21/2018


//These are specific to this game / client
let gameId;
let myTeam;

//current game state variables
let gamePhase;
let gameTurn;
let gameCurrentTeam;

//gamePoints variables
let gameRedRpoints;
let gameBlueRpoints;
let gameRedHpoints;
let gameBlueHpoints;

//gameBattle variables
let gameBattlePosSelected;
let gameBattleSection;
let gameBattleSubSection;
let gameBattleLastRoll;
let gameBattleLastMessage;
let gameBattleAdjacentArray;

//island variables
let islands = [];

//helper variables
let myRpoints;
let myHpoints;

//These current as of 09/21/2018
let phaseNames = ['News', 'Buy Reinforcements', 'Combat', 'Fortify Move', 'Reinforcement Place', 'Hybrid War', 'Tally Points'];
let unitNames = ['transport', 'submarine', 'destroyer', 'aircraftCarrier', 'soldier', 'artillery', 'tank', 'marine', 'lav', 'attackHeli', 'sam', 'fighter', 'bomber', 'stealthBomber', 'tanker', 'missile'];
let unitsMoves = [2, 2, 2, 2, 1, 1, 2, 1, 2, 3, 1, 4, 6, 5, 5, 0];


function gameGetState() {
    let phpGetVariables = new XMLHttpRequest();
    phpGetVariables.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            let json = JSON.parse(this.responseText);
            gameId = json.gameId;
            myTeam = json.myTeam;
            gamePhase = json.gamePhase;
            gameTurn = json.gameTurn;
            gameCurrentTeam = json.gameCurrentTeam;
            gameRedRpoints = json.gameRedRpoints;
            gameBlueRpoints = json.gameBlueRpoints;
            gameRedHpoints = json.gameRedHpoints;
            gameBlueHpoints = json.gameBlueHpoints;
            gameBattlePosSelected = json.gameBattlePosSelected;
            gameBattleSection = json.gameBattleSection;
            gameBattleSubSection = json.gameBattleSubSection;
            gameBattleLastRoll = json.gameBattleLastRoll;
            gameBattleLastMessage = json.gameBattleLastMessage;
            gameBattleAdjacentArray = json.gameBattleAdjacentArray;

            islands = json.islands;
            alert(islands);  //remove after testing
            islandOwnerChange();

            myRpoints = (myTeam === "Red") ? gameRedRpoints : gameBlueRpoints;
            myHpoints = (myTeam === "Red") ? gameRedHpoints : gameBlueHpoints;
        }
    };
    phpGetVariables.open("GET", "gameGetState.php", true);  // removes the element from the database
    phpGetVariables.send();
}


function islandOwnerChange(){
    for (let x = 0; x < islands.getLength(); x++) {
        document.getElementById("special_island" + (x+1)).classList[0] = islands[x];
        document.getElementById("special_island" + (x+1) + "_pop").classList[0] = islands[x];
    }
    document.getElementById("special_island5_extra").classList[0] = islands[4];
}

































