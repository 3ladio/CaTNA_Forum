document.addEventListener('click', function(e) {
    const link = e.target.closest('.ccrm-topic-card a');

    if (!link) return;

    e.preventDefault();

    const url = link.href;

    fetch(url)
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const topicContent = doc.querySelector('#bbpress-forums');

            if (topicContent) {
                document.querySelector('#ccrm-community-right').innerHTML = '';
                document.querySelector('#ccrm-community-right').appendChild(topicContent);
            }
        });
});

document.addEventListener('click', function(e) {
    const btn = e.target.closest('.ccrm-filter-btn');
    if (!btn) return;

    // Toggle active button
    document.querySelectorAll('.ccrm-filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const filter = btn.dataset.filter;
    const userGroups = ccrmUserGroups.groups || [];

    document.querySelectorAll('.ccrm-topic-card').forEach(card => {
        const topicGroups = (card.dataset.groups || '').split(',');

        if (filter === 'all') {
            card.style.display = 'block';
            return;
        }

        // My Groups filter
        const match = topicGroups.some(g => userGroups.includes(g));
        card.style.display = match ? 'block' : 'none';
    });
});

document.addEventListener('click', function(e) {
    const btn = e.target.closest('.ccrm-tab-btn');
    if (!btn) return;

    document.querySelectorAll('.ccrm-tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const tab = btn.dataset.tab;

    document.querySelectorAll('.ccrm-tab').forEach(t => t.classList.remove('active'));
    document.querySelector('#ccrm-tab-' + tab).classList.add('active');
});
