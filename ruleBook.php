<!DOCTYPE html>
<html>
<title>W3.CSS Template</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<style>
    body,h1,h2,h3,h4,h5 {font-family: "Raleway", sans-serif}
</style>
<body class="w3-light-grey">

<!-- w3-content defines a container for fixed size centered content,
and is wrapped around the whole page content, except for the footer in this example -->
<div class="w3-content" style="max-width:1400px">

    <!-- Header -->
    <header class="w3-container w3-center w3-padding-32">
        <h1><b>RULE BOOK</b></h1>
        <p>Welcome to  <span class="w3-tag">ISLAND RUSH</span></p>
    </header>

    <!-- Grid -->
    <div class="w3-row">

        <!-- Blog entries -->
        <div class="w3-col l8 s12">

            <!-- Blog entry -->
            <div class="w3-card-4 w3-margin w3-white">
                <div class="w3-container">
                    <h3><b>Opening Scenario</b></h3>
                </div>

                <div class="w3-container">
                    <p>In the South Züün Sea area, two countries, Züün (Red) and Vestrland (Blue), are in dispute over territory.
                        Züün has expanded its claims as the rightful owner and to legitimize these claims, Züün has annexed several existing islands and has built artificial islands.  There is a large amount of military activity (patrols, airstrip development, forward operating bases) on these islands in the South Züün Sea in order to support their claims and prevent outside forces from contesting these claims.
                        Züün’s major staging point is Züünport on Dragon Island.
                        Vestrland, with vested interests in the region, has grown worried and the political and military leadership of the country has decided that this expansion by Züün cannot go unchecked.  Therefore, they have decided to send additional military forces to their Naval and Air bases in the region.
                        Vestrland has one foothold in the area, Vestrpoint on Eagle Island.
                        While each individual country may send reinforcements from their mainland, their main influence and operations in the region are dependent on the one node on each side.  Züün is maneuvering to cut off Eagle Island from all external support, while Vestrland aims to take over the artificial and natural islands in the area and capture Züünport.
                    </p>

                </div>
            </div>



            <div class="w3-card-4 w3-margin w3-white">
                <div class="w3-container">
                    <h3><b>Map Legend</b></h3>
                </div>

                <div class="w3-container">
                    <p><img src="resources/mapImages/grid_special_island1.png" height="50" width="50"><b>Yellow Triangle:</b> Indicates the presence of an airfield
                    </p>
                    <p><img src="resources/mapImages/grid_special_island2.png" height="50" width="50"><b>Red Square:</b> Indicates the presence of a Land Based Sea Missile Site
                    </p>

                </div>
            </div>

            <div class="w3-card-4 w3-margin w3-white">
                <div class="w3-container">
                    <h3><b>Teams</b></h3>
                </div>

                <div class="w3-container">
                    <p>
                        <b>Combatant Commander:</b>
                        Fitted with all the power of the United States Department of Defense, you will be in command of all military branches. You would be most wise to delegate the majority of decisions and allow your service commanders to operate within their branches, but you are still given the final authority to approve or deny any decisions. Be sure to call in reinforcements for your commanders and ensure they all work together: a chain is only as strong as their weakest link. All timed choices will be your responsibility and if the clock runs out it will be on your shoulders. Act wisely and lead your team to a final victory.
                    </p>
                    <p>
                        <b>Air Force Commander:</b>
                        You have been chosen as the Air Force Commander. You yield the full power of all Air Force assets in the area and will be in command of your team. They have placed their trust in you: lead them well. In your command role, you will be responsible for ensuring the assets under your command are utilized well and committing these forces to action. You will also be the chief head in deriving strategy and executing all plans. You will have the support of your team: employ them well. We wish you good luck in your endeavors. Aim high airmen.
                    </p>
                    <p>
                        <b>Navy Commander:</b>
                        As the Navy Commander you will be in charge of all naval assets. America’s fleet has been a global force for good and now your job is to lead them. Your job is to pursue freedom and those who threaten it. In this role, you will be tasked with a team to support and advise your decisions. In the end, you will be responsible for ensuring the assets under your command are utilized well and committing these forces to action. As a commander, you will also be the leading mind in strategizing and executing all plans. All maritime components under your jurisdiction have been mobilized to support your efforts. Anchors Away.
                    </p>
                    <p>
                        <b>Army Commander:</b>
                        You have been chosen as the newest Army Commander and it is time to wrestle up your ground pounders. All ground troops and their massive fire power will be under your command. Your mission is to lead your team to be employing all assets under you. Keep your team close and lead by example. In order to act as a true leader, you will be responsible for ensuring the assets under your command are utilized well and committing these forces to action. Your strategy will be your highest priority but prepare to take advice from your team of trusted advisors. Finally, you will be in control of all land components and must be prepared to lead them to victory. Be Army strong.
                    </p>
                    <p>
                        <b>Marines Commander:</b>
                            You have been selected and worked your way up through the ranks of some of the roughest and toughest men and women in the world: the Marines. As the Marine Commander, you understand that many of your Marines will be sent into perilous situations but their bravery and courage will carry themselves and their comrades home. Their primary duty is to follow you while you bear the burden of leading them through the planning and all strategies that your branch will take part in. Do not forget that these resources have been placed under your command and are completely your responsibility. This is your chance to lead the few and the proud.
                    </p>
                    <p>
                        <b>Co-Commanders:</b>
                        Your sole purpose is to back your commander. When they are present you will support them in all decisions in public while acting as a confidant and advisor in private. In the absence of your respective commander, your job will be to act in their stead. All of your decisions will be seen as their decisions so act wisely. You should familiarize yourself with all commander positions and responsibilities.
                    </p>

                </div>
            </div>

            <div class="w3-card w3-margin">
                <div class="w3-container w3-padding">
                    <h4><b>Turns and Phases</b></h4>
                </div>
                <ul class="w3-ul w3-hoverable w3-white">
                    <li class="w3-padding-16">
                        <span class="w3-large"><b>Phase 1: News Alerts</b></span><br>
                        <span>Teams will start the first phase of their turn, and they will receive a “News Alert” to start the turn.  News Alerts will occur at the beginning of each team’s turn every turn. Some news alerts will have minimal effect on the game, while others may alter circumstances significantly.
                        </span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><b>Phase 2: Call for Reinforcements</b></span><br>
                        <span>Each team has the option to call for reinforcements from their home nation. In this phase a team will use available reinforcement points to purchase units, which the will gain at the start of this phase depending on how many and which islands they hold. These available units will be indicated by their presence in the “shop/inventory.” Teams may spend all, some, or none of their points when calling in reinforcements. Points carry over between turns. Teams are not allowed to incur debt or spend more points than they have for that turn. Drag troops from the inventory to the “Trash” to refund the points.  Hover your mouse over a unit to see its price and moves. The team will be given 5 minutes to start their turn and purchase units. After 5 minutes, the combat phase begins.</span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><b>Phase 3: Combat</b></span><br>
                        <span>This phase encompasses all offensive operations for that team’s turn.This phase includes
                              land, sea, and air combat. In order to execute an attack, the aggressor team must must
                              move the forces they are using to conduct the attack adjacent or in the contested zone.
                              Once all desired pieces are in position in or around the zone that is being attacked, Click the “Select Battle” button. (Reminder: aircraft must take off of Carriers to participate in battle.) Then select the zone and it will highlight yellow. Once the zone that you want to attack is selected, click the “Select pieces” button. Then click on the pieces you want to use in the battle. Once all desired attackers are selected, Click the “Start Battle” button. The Battle Zone will popup and the battle will begin.
                              <b>Note: Destroyers in the sea zones next to an island can be selected for a land battle
                                  to Bombard enemy land units. Destroyers can only bombard one enemy unit in that
                                  battle, then they will fall back, using their attack for that turn. If an enemy unit
                                  is hit by bombardment, they do not get a defense bonus.</b> </span>
                    <li><span><b>Rounds of Combat:</b> Combat consists of a number of rounds, where the attacker uses all of
                                  their units, then the defender uses all of their units to fight back in a
                                      counter attack. After this whole sequence, the attacker can choose to continue
                                      another round of battle, or retreat.</span></li>


                        <span><li><b>Movement:</b> Units may be moved up, down, left, right, or in any of the four
                                        diagonal directions.  The most basic explanation is that moving into a different
                                        zone or territory counts as one movement. In this way, each movement from one
                                        grid space to the next counts as a move, and each movement from one section
                                        of an island to another counts as one movement.  You can only drag pieces on
                                        the board one zone at a time. Moving onto a transport or off of a transport
                                      counts as one movement each.</span></li>

                    <li><span><img src="resources/mapImages/battle_zone.png" height="400" width="300"></span></li>
                    <li><b>Battle Zone:</b>  The battle zone is where all combat occurs.  When a battle starts,(described above) all defending units in the space where combat is involved and selected attacking units will be moved into the corresponding “unused unit” sections of the battle zone.  To attack a unit, click both units to move them into the center of the battle zone. Once a unit has attacked, it is then moved into the “used unit” section. The attacker, no matter the team, is on the right side of the Battle Zone.</span></li>
                    <li><span><b>Hit Determination:</b> Once the units are selected to attack or defend, the units will appear in their respective areas in the center of the battle zone.  When you click the “Attack!” button, the attacking unit has a chance to destroy the defending unit, based on the Attack Matrix. If the attacker misses, it is moved to the “Used” section for the rest of this round of battle. If the attacker hits, the defending unit rolls a defense bonus, where the defender can either survive the attack or destroy the attacker with it, based on its capability to strike back. The attack value is the minimum value that the attacking unit must secure in order to score a hit. Any lower value is considered a miss.  Attack values can be found on the “Attack Matrix” chart, with values determined by both the attacking and defending units.</span></li>
                    <li><span><b>Defense Bonus:</b> If a defending unit is hit, it immediately rolls for a defense bonus against whatever unit attacked it. The value needed for a defense bonus hit is the attack value for that defending troop fighting that attacking unit. If the defender scores a hit, then both units are destroyed, and if the defending unit fails, it is still destroyed. For units that do not have an attack value against the attacking unit, they must score the value of six to succeed in a defense bonus, and they will survive upon a successful roll since they are not capable of destroying whatever attacked them. Counter Attack: Once the attacker has used all of their units in the battle zone or decides to stop attacking, it is the defender’s turn to counter attack.  This is played out in a similar way to the initial attack, however, attacking units that are hit by counter-attacking defensive units cannot roll for a defense bonus and are destroyed immediately. All unused defensive units may counterattack.</span></li>
                    <li><span><b>Ending Combat:</b>  Once the counterattack is complete, the attacker may resume the next round of attack if there are units left. The attacker may also choose to retreat; in which case all units will be pulled out of the battle zone.</span></li>
                    <li><span><b>Note: If an attacker hits and destroys all defending units, teams will still need to proceed to the Counter Attack then immediately End Counter Attack using the buttons on the Battle Zone for ending battles on the computer.</b></span></li>
                    <li><span><b>Island Capture:</b> Islands can only be captured after a team has taken the command post of the island with land units, this zone is denoted by the black flag. [Note: Airfields cannot be used by a team until that team owns the command post of that island regardless of whether they have captured the airfield.] In addition, if an island is captured where enemy aircraft are still remaining on the island’s airfield, these aircraft must retreat and will be destroyed if they are not able to do so.  When this is executed in the game, the aircraft will remain in place until the defending team’s turn and they must move them at this point.  If they are still unable, the aircraft will be destroyed.</span></li>

                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><b>Phase 4: Fortification Movement</b></span><br>
                        <span>This phase allows a player to move their units only after the combat phase is over. No combat may be performed in this phase to include capturing unoccupied islands. Teams may move any units that have moves left. For example, if an armor unit moved to attack an adjacent land territory, it has only one move remaining for the fortification phase. This entire phase will take no more than 5 minutes and be monitored by the Combatant Commander.
                                Note:This phase is where Tankers can extend the moves of other aircraft. Move tankers into the same zone as other aircraft during Combat phase, because as soon as this phase begins, aircraft moves will be extended if they are in the same zone as a Tanker.
                        </span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><b>Phase 5: Reinforcement</b></span><br>
                        <span>This phase allows teams to place the reinforcements they ‘purchased’ at the beginning of the turn. All reinforcements must be placed during the reinforcement phase or will be sent home. Reinforcements can only be placed on the team’s capital island or the sea zones that surround it and aircraft may only be placed on airstrips on the capital island, not aircraft carriers.  Note: Reinforcements cannot be placed in zones (land or sea) that are being occupied by enemy units.
                        </span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><b>Phase 6: Hybrid Warfare Option:</b></span><br>
                        <span>This phase gives teams the option to use their hybrid points from capturing islands. All hybrid warfare options take immediate effect and last as long as their individual selection reads. Please see “Hybrid Warfare” section for further details. This phase will take no more than 2 minutes. Final hybrid warfare selection is the responsibility of the Combatant Commander.
                              Zuun will follow with the same turn sequence followed by Vesterland again and so on. Each defending team is responsible for making sure the opposing team is staying within the time limit.
                        </span>
                    </li>
                </ul>
            </div>

            <div class="w3-card w3-margin">
                <div class="w3-container w3-padding">
                    <h4><b>Hybrid Warfare Phase Details</b></h4>
                </div>
                <div class="w3-container w3-padding">
                    This is the non-conventional warfare portion of the game. The following are options for the Combatant Commander to choose how to spend their hybrid warfare (HW) points. HW points are earned by capturing islands, 1 point per island capture.  Teams may spend all, some, or none of the points in their bank. Points carry over between turns.
                </div>
                <ul class="w3-ul w3-hoverable w3-white">
                    <li class="w3-padding-16">
                        <span class="w3-large"><b>Air Traffic Control Scramble – 3 points:</b></span><br>
                        <span>This cyber-attack causes an enemy airfield to be shut down for the following turn. Teams will choose a specific airfield anywhere on the map to shut down. Aircraft stationed on that airfield may not takeoff or move for the entire enemy team’s turn. No aircraft shall be newly stationed to affected airfield.
                        </span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><b>Bank Drain – 4 points:</b></span><br>
                        <span>This cyber-attack causes the value of an enemy island to count toward your reinforcement point total for the next two turns . Additionally, the same island will NOT count toward the reinforcement total of the opposing team. Note: Teams may choose any island regardless of location, (Exception: Capital islands cannot be chosen for this type of attack).
                        </span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><b>Advanced Remote Sensing- 8 points:</b></span><br>
                        <span>A new satellite has found a way to temporarily shorten all logistical routes. For their next turn, all of a team’s units gain +1 movement points, (i.e. an infantry units movement moves from 1 to 2).
                        </span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><b>Rods from God  - 6 points:</b></span><br>
                        <span>New satellite technology allows for kinetic effects from space! A team may choose one unit of any kind anywhere on the map to target. This unit is instantly destroyed and removed from the board without a defense bonus. Note: Any units inside or stationed on targeted units shall be destroyed as well (Example: If an aircraft carrier is targeted, any fighters stationed on it are also destroyed).
                        </span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><b>Goldeneye – 10 points:</b></span><br>
                        <span>A high altitude burst Intercontinental Ballistic Missile (ICBM) detonation produces an electromagnetic pulse over all enemy aircraft on the map. This freezes all enemy aircraft for their next turn. No aircraft shall take off from their airfields or shall be called in for reinforcements for that turn.
                        </span>
                    </li>
                    <li class="w3-padding-16">
                        <span class="w3-large"><b>Nuclear Strike – 12 points:</b></span><br>
                        <span>An ICBM ground burst strike destroys an island. Any units stationed on or in the sea zones adjacent to the target island will be immediately destroyed. This island will suffer from nuclear fallout and shall not be used in any capacity for the remainder of the game. Sea zones surrounding the island can be used normally in following turns. Any news alerts no longer apply to the targeted island. Additionally, no team can collect reinforcement or hybrid points from this island. This does not affect any points not spent from previous turns. (Note: Nuclear Strike cannot be used on capital islands.)
                        </span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><b>Humanitarian Aid - 3 points:</b></span><br>
                        <span>When a News Alert notifies a team about a natural disaster or other catastrophe in an area, teams have the option to provide humanitarian aid to that island.  Teams who provide humanitarian aid receive 10 reinforcement points usable the turn following aid rendered.
                        </span>
                        (NOTE: Humanitarian Aid is disbled for 3 turns after 'Goldeneye' or 'Nuclear Strike' has been used.)
                        <span
                    </li>
                </ul>
            </div>

            <div class="w3-card w3-margin">
                <div class="w3-container w3-padding">
                    <h4><b>Forces</b></h4>
                </div>
                <ul class="w3-ul w3-hoverable w3-white">
                    <li class="w3-padding-16">
                        <span class="w3-large"> <img src="resources/unitImages/soldier.png" alt="InfantryImage" height="65" width="65"><b>Infantry Company:</b></span><br>
                        <span>Consists of 150 soldiers at full strength and is able move 1 space.</span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"> <img src="resources/unitImages/artillery.png" height="65" width="65"><b>Artillery Battery:</b></span><br>
                        <span>Consists of 10 cannons and is able to move 1 space.</span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><img src="resources/unitImages/tank.png" height="65" width="65"><b>Tank Platoon:</b></span><br>
                        <span>Consists of 4 tanks and is able to move 1 space.</span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><img src="resources/unitImages/missile.png"height="65" width="65"><b>Land Based Sea Missiles:</b></span><br>
                        <span>Land based sea missiles are one of the two automatically activated units.  Their sites are denoted by a red box on an island and can contain one missile at a time.  These sites enable islands to attack surface naval units. Their range consists of the sea zones surrounding an island.  Each site must have missiles purchased for it during the reinforcement phase. Once a ship moves into the area surrounding the LBSM, each missile has as an 80% chance of hitting any type of surface ship.</span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><img src="resources/unitImages/attackHeli.png" height="65" width="65"><b>Attack Helicopters:</b></span><br>
                        <span>Consists of a 2 ship of attack helicopters and is able to move up to 3 spaces.  Helicopters can fly over land or sea but must end the turn on land.</span>
                    </li>
                    <li class="w3-padding-16">
                        <span class="w3-large"><img src="resources/unitImages/marine.png" height="65" width="65"><b>Marine Convoy:</b></span><br>
                        <span>Consists of 10 vehicles and is able to move 2 spaces.</span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><img src="resources/unitImages/destroyer.png" height="65" width="65"><b>Destroyer:</b></span><br>
                        <span>This unit is able to move 2 spaces and detect submerged units</span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><img src="resources/unitImages/aircraftCarrier.png" height="65" width="65"><b>Aircraft Carrier:</b></span><br>
                        <span>This is able to move 2 spaces and can carry up to two fighter squadrons. If the fighters on a carrier are planned to be in an attack, they must take off (be removed from) the carrier before combat starts. The carrier may move during the combat phase so the fighters take off from a closer location to the target even if the carrier itself does not participate in combat.</span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><img src="resources/unitImages/submarine.png" height="65" width="65"><b>Submarines:</b></span><br>
                        <span> This unit is able to move 2 spaces and can only be detected by destroyers or other submarines if they are not attacking.  Submarines may pass through enemy occupied sea zones, unless they have a destroyer or submarine. In this case, the submarine must act as an attacking force.</span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><img src="resources/unitImages/transport.png" height="65" width="65"><b>Transport:</b></span><br>
                        <span>This unit is able to move up to 2 spaces and has no attack value, but it may defend itself. This unit may carry ONE of the following combinations:
<b>1)</b> 3 Army Infantry Companies/Marine Infantry Platoons,
<b>2)</b> 1 Army Infantry Company/Marine Infantry Platoon and 1 Tank Platoon,
<b>3)</b> 1 Army Infantry Company/Marine Infantry Platoon and 1 Marine Convoy,
<b>4)</b> 1 Army Infantry Company/Marine Infantry Platoon and 1 Helicopter unit,
<b>5)</b> 1 Army Infantry Company/Marine Infantry Platoon and 1 SAM Battery,
<b>6)</b> 1 Army Infantry Company/Marine Infantry Platoon and 1 Artillery Battery
Transports may only move one set of units (as defined above) per turn.</span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><img src="resources/unitImages/bomber.jpg" height="65" width="65"><b>Bomber Squadron:</b></span><br>
                        <span>Consists of a 2 ship of bombers and can move 6 spaces.</span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><img src="resources/unitImages/fighter.png" height="65" width="65"><b>Fighter Squadron:</b></span><br>
                        <span>Consists of a 4 ship of fighters.  This unit can move 4 spaces and can land on an aircraft carrier.</span>

                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><img src="resources/unitImages/stealthBomber.png" height="65" width="65"><b>Stealth Bomber:</b></span><br>
                        <span>Consists of a 2 ship of stealth bombers and can move 5 spaces.</span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><img src="resources/unitImages/tanker.png" height="65" width="65"><b>Tanker:</b></span><br>
                        <span>Consists of 1 KC-135 and able to move up to 5 spaces. It has no attack value.  The tanker extends the moves of all bombers by 3 and fighters by 2. The tanker is only able to refuel during the reinforcement face, so an aircraft’s range cannot be extended on the way to combat. Does not apply to helicopters.</span>
                    </li>

                    <li class="w3-padding-16">
                        <span class="w3-large"><b>AIRCRAFT NOTES:</b></span><br>
                        <span> When attacking, aircraft only have the fuel reserves and ammunition to participate in the first two rounds of an attack regardless if they scored hits or not in those rounds. For example, if two fighters attack an island zone containing 5 tank platoons, the maximum number of casualties the defending team can suffer is 4 platoons (2 hits in two rounds of attack). This applies to all aircraft excluding the attack helicopters.  When defending a territory, aircraft have no limit on the combat rounds they can participate in.  Aircraft also cannot be sent on ‘suicide runs’ where they attack an area that is too far from any friendly airfield or carrier (This is currently allowed by the game but doctrinally is unacceptable). Meaning that an aircraft must have enough moves to get to the target and get to a friendly airfield (or for fighters aircraft carriers) to land.  Aircraft cannot engage submarines.
                        </span>
                    </li>
                </ul>
            </div>

            <!-- END BLOG ENTRIES -->
        </div>
        <!-- END GRID -->
    </div><br>

    <!-- END w3-content -->
</div>


</body>
</html>
