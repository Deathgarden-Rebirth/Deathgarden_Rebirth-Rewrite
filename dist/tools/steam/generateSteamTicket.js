import dotenv from 'dotenv'
import SteamUser from "steam-user";

dotenv.config();

if (process.env.DEV_STEAM_ACCOUNT_NAME === '') {
    console.log('Environment key "DEV_STEAM_ACCOUNT_NAME" is empty. Aborting');
    process.exit(1);
}

if (process.env.DEV_STEAM_ACCOUNT_PASSWORD === '') {
    console.log('Environment key "DEV_STEAM_ACCOUNT_PASSWORD" is empty. Aborting');
    process.exit(1);
}

const steamUser = new SteamUser();
steamUser.logOn({ accountName: process.env.DEV_STEAM_ACCOUNT_NAME, password: process.env.DEV_STEAM_ACCOUNT_PASSWORD });
// If 2FA is enabled, it will interactively ask for the token

steamUser.on('loggedOn', function() {
    console.log(`Logged into Steam as ${steamUser.steamID.getSteam3RenderedID()}`);

    // Make the user play the game
    steamUser.gamesPlayed(555440, true);

    // Requests a signed session ticket
    steamUser.createAuthSessionTicket(555440, (err, ticket) => {
        console.log(err); // Should be null
        console.log(ticket.toString('hex')); // Print it as hex string
        process.exit();
    });
});