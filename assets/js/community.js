document.addEventListener('click', function(e) {
    const nav = e.target.closest('.ccrm-nav-item');
    if (!nav) return;

    const panel = nav.dataset.panel;

    if (panel === 'community') {
        initializeCommunityPanel();
    }
});

function initializeCommunityPanel() {
    // Prevent double-initialization
    if (window.ccrmCommunityInitialized) return;
    window.ccrmCommunityInitialized = true;

    console.log('Community panel initialized');

    // Re-run the topic click handler
    attachTopicClickHandlers();

    // Re-run the filter logic
    attachFilterHandlers();

    // Re-run the tab logic (Forums / Volunteer Opportunities)
    attachTabHandlers();

    attachForumClickHandlers();

    // Reveal the panel
    document.querySelector('#ccrm-panel-community').classList.add('ccrm-ready');
}

function attachForumClickHandlers() {
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.ccrm-forum-open-btn');
        if (!btn) return;

        e.preventDefault();

        const forumUrl = btn.dataset.forumUrl;

        fetch(forumUrl)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const topicLinks = doc.querySelectorAll('.bbp-topic-title a');

                const left = document.querySelector('#ccrm-community-left');
                left.innerHTML = '';

                topicLinks.forEach(link => {
                    const card = document.createElement('div');
                    card.classList.add('ccrm-topic-card');
                    card.innerHTML = `
                        <h3 class="ccrm-topic-title">
                            <a href="${link.href}">${link.textContent}</a>
                        </h3>
                    `;
                    left.appendChild(card);
                });
            });
    });
}

function attachTopicClickHandlers() {
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
                if (!topicContent) return;

                // Create wrapper BEFORE inserting content
                const wrapper = document.createElement('div');
                wrapper.classList.add('ccrm-topic-detail');
                wrapper.appendChild(topicContent);

                // Insert into right panel
                const right = document.querySelector('#ccrm-community-right');
                right.innerHTML = '';
                right.appendChild(wrapper);

                // Add styled header
                const header = wrapper.querySelector('.bbp-topic-title')?.parentNode;
                if (header) header.classList.add('ccrm-topic-detail-header');
            });
    });
}

function attachFilterHandlers() {
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
}

function attachTabHandlers() {
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.ccrm-tab-btn');
        if (!btn) return;

        document.querySelectorAll('.ccrm-tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const tab = btn.dataset.tab;

        document.querySelectorAll('.ccrm-tab').forEach(t => t.classList.remove('active'));
        document.querySelector('#ccrm-tab-' + tab).classList.add('active');
    });
}

