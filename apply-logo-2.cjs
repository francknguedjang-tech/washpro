const fs = require('fs');
const path = require('path');

const replacements = [
    {
        file: 'resources/views/welcome.blade.php',
        rules: [
            { regex: /<i class="bi bi-droplet-fill text-gradient fs-2"><\/i> washpro/g, replacement: `<img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 36px; width: auto;">` },
            { regex: /<h4 class="text-white fw-bold mb-4"><i class="bi bi-droplet-fill text-primary"><\/i> washpro<\/h4>/g, replacement: `<img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 48px; width: auto;" class="mb-4">` }
        ]
    },
    {
        file: 'resources/views/client/dashboard.blade.php',
        rules: [
            { regex: /<i class="bi bi-droplet-fill text-gradient fs-3"><\/i> washpro/g, replacement: `<img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 32px; width: auto;">` }
        ]
    },
    {
        file: 'resources/views/client/settings.blade.php',
        rules: [
            { regex: /<i class="bi bi-droplet-fill text-gradient fs-3"><\/i> washpro/g, replacement: `<img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 32px; width: auto;">` }
        ]
    },
    {
        file: 'resources/views/client/help.blade.php',
        rules: [
            { regex: /<i class="bi bi-droplet-fill text-gradient fs-3"><\/i> washpro/g, replacement: `<img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 32px; width: auto;">` }
        ]
    },
    {
        file: 'resources/views/auth/verify-email.blade.php',
        rules: [
            { regex: /<i class="bi bi-water"><\/i>WashPro/g, replacement: `<img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 48px; width: auto;" class="mb-3">` }
        ]
    },
    {
        file: 'resources/views/auth/register.blade.php',
        rules: [
            { regex: /<i class="bi bi-water me-2"><\/i>WashPro/g, replacement: `<img src="{{ asset('img/logo.svg') }}" alt="WashPro Logo" style="height: 48px; width: auto;" class="mb-3">` }
        ]
    }
];

replacements.forEach(item => {
    let p = path.join(__dirname, item.file);
    if (fs.existsSync(p)) {
        let content = fs.readFileSync(p, 'utf8');
        let orig = content;
        item.rules.forEach(r => {
            content = content.replace(r.regex, r.replacement);
        });
        if (content !== orig) {
            fs.writeFileSync(p, content, 'utf8');
            console.log('Updated: ' + item.file);
        }
    }
});
