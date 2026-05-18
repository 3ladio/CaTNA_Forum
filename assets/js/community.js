/**
 * CaTNA CRM Community SPA JavaScript Engine
 */

// Global Event Delegation for Clicks
document.addEventListener('click', function(e) {
    // 1. Sidebar Panel Navigation Controller
    const nav = e.target.closest('.ccrm-nav-item');
    if (nav && nav.dataset.panel === 'community') {
        initializeCommunityPanel();
        return;
    }

    // 2. Click Handler for Selecting a Topic Card
    const topicCard = e.target.closest('.ccrm-topic-card');
    if (topicCard) {
        e.preventDefault();
        handleTopicCardSelection(topicCard);
        return;
    }

    // 3. Click Handler for triggering New Thread UI
    const newThreadBtn = e.target.closest('#ccrm-new-thread-trigger');
    if (newThreadBtn) {
        e.preventDefault();
        handleNewThreadTrigger(newThreadBtn);
        return;
    }
});

// Global Event Delegation for Submissions (Intercepting Form Posting)
document.addEventListener('submit', function(e) {
    const topicForm = e.target.closest('.ccrm-new-thread-container #new-post');
    if (!topicForm) return;

    // Halt standard browser page redirection execution paths
    e.preventDefault();
    handleSPAFormSubmission(topicForm);
});

function initializeCommunityPanel() {
    // Stop duplicate initialization entirely
    if (window.ccrmCommunityInitialized) return;
    window.ccrmCommunityInitialized = true;

    console.log('CaTNA Community Panel Interfaced Successfully.');

    // Gracefully fade in or reveal panel UI
    const communityPanel = document.querySelector('#ccrm-panel-community');
    if (communityPanel) {
        communityPanel.classList.add('ccrm-ready');
    }
}

function handleTopicCardSelection(card) {
    // Toggle active visual states on cards
    document.querySelectorAll('.ccrm-topic-card').forEach(c => c.classList.remove('active'));
    card.classList.add('active');

    // Extract target discussion URL
    const targetLink = card.querySelector('.ccrm-load-topic-link');
    if (!targetLink) return;
    
    const url = targetLink.href;
    const rightPanel = document.querySelector('#ccrm-community-right');
    
    if (!rightPanel) return;

    // Provide immediate interactive loading state feedback
    rightPanel.innerHTML = `
        <div style="display: flex; justify-content: center; align-items: center; height: 100%; min-height: 200px; color: #6b7280;">
            <p>Loading conversation thread...</p>
        </div>
    `;

    // Fetch bbPress single thread view safely
    fetch(url)
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extract core bbPress forums container completely bypassing main outer page wrapper
            const threadContent = doc.querySelector('#bbpress-forums');
            
            if (threadContent) {
                rightPanel.innerHTML = '';
                
                // Wrap content inside clean child container
                const detailWrapper = document.createElement('div');
                detailWrapper.classList.add('ccrm-topic-detail');
                detailWrapper.appendChild(threadContent);
                
                rightPanel.appendChild(detailWrapper);
            } else {
                rightPanel.innerHTML = '<p style="padding: 20px; color: red;">Error: Discussion could not load.</p>';
            }
        })
        .catch(err => {
            console.error('Fetch operation failed:', err);
            rightPanel.innerHTML = '<p style="padding: 20px; color: red;">Network timeout/error.</p>';
        });
}

function handleNewThreadTrigger(btn) {
    const rightPanel = document.querySelector('#ccrm-community-right');
    if (!rightPanel) return;

    // Provide immediate visual loading feedback inside the right container
    rightPanel.innerHTML = `
        <div style="display: flex; justify-content: center; align-items: center; height: 100%; min-height: 200px; color: #6b7280;">
            <p>Loading thread creation workspace...</p>
        </div>
    `;

    // Fetch the raw form components directly from our newly minted endpoint
    fetch('/wp-admin/admin-ajax.php?action=ccrm_get_topic_form')
        .then(res => res.json())
        .then(response => {
            if (response.success && response.data) {
                rightPanel.innerHTML = '';

                // Build a semantic parent content wrap to capture styles
                const formWrapper = document.createElement('div');
                formWrapper.classList.add('ccrm-topic-detail', 'ccrm-new-thread-container');
                formWrapper.innerHTML = response.data;

                rightPanel.appendChild(formWrapper);

                // Initialize quicktags/basic text toolbar elements if they exist
                if (typeof quicktags !== 'undefined') {
                    quicktags({ id: 'bbp_topic_content' });
                    QTags._init();
                }
            } else {
                rightPanel.innerHTML = `
                    <div style="padding: 20px; color: #ef4444;">
                        <p><strong>Form Unavailable:</strong> ${response.data?.message || 'Access authorization failure.'}</p>
                    </div>
                `;
            }
        })
        .catch(err => {
            console.error('Thread creation initialization failed:', err);
            rightPanel.innerHTML = '<div style="padding: 20px; color: #ef4444;"><p>Network context timeout.</p></div>';
        });
}

function handleSPAFormSubmission(form) {
    const submitBtn = form.querySelector('#bbp_topic_submit');
    const rightPanel = document.querySelector('#ccrm-community-right');
    if (!submitBtn || !rightPanel) return;

    submitBtn.disabled = true;
    const originalBtnText = submitBtn.innerText;
    submitBtn.innerText = 'Publishing thread...';

    const activeForumBtn = document.querySelector('.ccrm-forum-open-btn.active') || document.querySelector('[data-forum-id]');
    const dynamicForumId = activeForumBtn ? activeForumBtn.dataset.forumId : null;

    const formData = new FormData(form);
    formData.append('action', 'ccrm_submit_ajax_topic');

    if (dynamicForumId && (!formData.get('bbp_forum_id') || formData.get('bbp_forum_id') === '0')) {
        formData.set('bbp_forum_id', dynamicForumId);
    }

    fetch('/wp-admin/admin-ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(text => {
        try {
            const response = JSON.parse(text);
            
            if (response.success) {
                // SUCCESS STATE: Render the celebration completion card
                rightPanel.innerHTML = `
                    <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; min-height: 200px; text-align: center; padding: 20px;">
                        <span style="font-size: 3rem; margin-bottom: 15px;">🎉</span>
                        <h3 style="color: #008a76; margin-bottom: 8px; font-weight: 600;">Discussion Thread Published!</h3>
                        <p style="color: #6b7280; font-size: 0.95rem;">Your new discussion topic has been added to the community forum successfully.</p>
                    </div>
                `;

                // REFRESH SIDEBAR LIST
                const targetForumId = response.data.forum_id || dynamicForumId;
                if (targetForumId) {
                    // Added &t=${Date.now()} to force a fresh fetch and bypass browser cache
                    fetch(`/wp-admin/admin-ajax.php?action=ccrm_get_forum_topics&forum_id=${targetForumId}&t=${Date.now()}`)
                        .then(r => r.json())
                        .then(data => {
                            if (data.success && data.data) {
                                const parser = new DOMParser();
                                const incomingDoc = parser.parseFromString(data.data, 'text/html');
                                
                                // Precise Target: Only replace the content of the list wrapper
                                const listContainer = document.querySelector('.ccrm-topic-cards');
                                const freshList = incomingDoc.querySelector('.ccrm-topic-cards');
                                
                                if (listContainer && freshList) {
                                    listContainer.innerHTML = freshList.innerHTML;
                                    console.log('Sidebar topic list updated successfully.');
                                } else {
                                    console.warn('Could not find .ccrm-topic-cards container to update.');
                                }
                            }
                        })
                        .catch(err => console.error('Sidebar refresh failed:', err));
                }
            } else {
                alert('Submission Error: ' + (response.data?.message || 'Unknown processing rejection.'));
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;
            }
        } catch (jsonError) {
            console.error('Raw Server Output:', text);
            alert('Server Parsing Error. Response was: ' + text.substring(0, 300));
            submitBtn.disabled = false;
            submitBtn.innerText = originalBtnText;
        }
    })
    .catch(err => {
        console.error('Asynchronous pipeline execution failed:', err);
        alert('A network connectivity exception occurred.');
        submitBtn.disabled = false;
        submitBtn.innerText = originalBtnText;
    });
}