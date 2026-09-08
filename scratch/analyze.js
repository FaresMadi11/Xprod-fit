const fs = require('fs');
const content = fs.readFileSync('c:/xampp/htdocs/Xprod Fit/scratch/user_svg_interactive.txt', 'utf8');
const regex = /<path[^>]*d="([^"]+)"[^>]*>/g;
let match;
let i = 0;
while ((match = regex.exec(content)) !== null) {
    const d = match[1];
    if (d.length > 200) {
        console.log('Path ' + i + ': length ' + d.length);
    }
    i++;
}
