///////////////////////////////////////////////////////////////////////////////
//
// https://github.com/damian-pastorini/p2js-tiledmap-demo/blob/master/test-town.html
//
//////////////////////////////////////////////////////////////////////////////
var Bridge = require('../services/bridge.ts');
var p2 = require('p2');
const Mob = require("./mob");



  export function addCollisions(self){

  self.world.on('impact', (evt: any) => {
    var bodyA = evt.bodyA;
    var bodyB = evt.bodyB;
    if (bodyA.isPortal) {
      if (!bodyA.done) {
        console.log('Portal!!!!');
        console.log('tiled: ' + bodyA.tiled);
        console.log('playerId: ' + bodyB.playerId);
        const promise1 = Promise.resolve(Bridge.updateMap(bodyB.playerId, bodyA.destination));
        promise1
          .then(() => { bodyB.portal = bodyA.tiled; })
          .then(() => {
            console.log(bodyA.destination_x);
            Bridge.updatePlayer(bodyB.playerId, 'x', bodyA.destination_x, 1)
          })
          .then(() => {
            console.log(bodyA.destination_y);
            Bridge.updatePlayer(bodyB.playerId, 'y', bodyA.destination_y, 1)
          });
        bodyA.done = true;
      }
    }
    if (bodyA.isReward) {
      if (!bodyA.done) {
        const promise1 = Promise.resolve(Bridge.updatePlayer(bodyB.playerId, 'credits', 1, 0));
        promise1.then(() => { });
        const promise2 = Promise.resolve(Bridge.updatePlayer(bodyB.playerId, 'experience', 1, 0));
        promise2.then(() => { });
        self.broadcast("remove-reward", bodyA.ref);
        bodyB.reward = bodyA.ref;
        bodyA.done = true;
      }
    }
    if (bodyA.isMob && bodyA.dead == 0) {
      //  if (!bodyA.done) {
      console.log('Mobstrike!!!!');
      console.log('playerId: ' + bodyB.playerId);
      console.log('health: ' + bodyB.health);
      bodyB.struck = true;
      const promise1 = Promise.resolve(Bridge.updatePlayer(bodyB.playerId, 'health', -10, false));
      promise1.then(() => {
        bodyB.health = bodyB.health - 10;

        if (bodyB.health <= 0) {
          //bodyB.health = 0;
          const promise1 = Promise.resolve(Bridge.updatePlayer(bodyB.playerId, 'health', 80, true));
          self.broadcast("dead", bodyB.playerId);
        }
      });
      //When zombie is dead set dead health  and following
      Mob.updateZombieState(
        bodyA.field_mobs_target_id, bodyA.field_mob_name_value, parseInt(bodyA.position[0]), parseInt(bodyA.position[1]),
        0, 0, 1, undefined, undefined);
      self.broadcast("player hit", bodyA.field_mob_name_value); // TODO change to player name
      bodyB.mobHit = bodyA.mob_name;
      //bodyA.done = true;
      //     }
    }
  })
  ///////////////////////////////////////////////////////////////////////////
  self.world.on('beginContact', function (evt: any) {
    var bodyA = evt.bodyA;
    var bodyB = evt.bodyB;
    console.log('Begin Contact');
    if (bodyA.isPlayer) {
      /*        console.log('endContact! --------------------------------------------------------------');
                console.log('BODY A is the player!', bodyA.isPlayer, bodyA.id);
                console.log('BODY B is the wall!', bodyB.isWall, bodyB.id,  bodyB.position); */
      console.log('BODY B TILE / TILEINDEX: ', bodyB.tile, bodyB.tileIndex);
      bodyA.collide = true;
      //    bodyA.velocity = [0, 0];
    } else {
      /*   console.log('endContact! --------------------------------------------------------------');
        console.log('BODY A is the wall!', bodyA.isWall, bodyA.id, bodyA.position);
        console.log('BODY B is the player!', bodyB.isPlayer, bodyB.id); */
      console.log('BODY A TILE / TILEINDEX: ', bodyA.tile, bodyA.tileIndex);
      bodyB.collide = true;
      //  bodyB.velocity = [0, 0];
    }
    console.log('----- .');
  });
  self.world.on("endcontact", function (evt: any) {
    var bodyA = evt.bodyA;
    var bodyB = evt.bodyB;
    console.log('-----------End Contact--- Pay Attention---');
  });
}
