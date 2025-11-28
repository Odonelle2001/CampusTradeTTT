<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: LoginPage.php');
    exit;
}

include 'header.php';

$userId    = (int) $_SESSION['user_id'];
$userName  = !empty($_SESSION['firstName'])
    ? $_SESSION['firstName']
    : (!empty($_SESSION['email']) ? $_SESSION['email'] : 'Student User');
$userEmail = $_SESSION['email'] ?? '';

$db = require __DIR__ . '/Database.php';

// Load profile data for logged-in user
$profileSql = "
    SELECT
        a.first_name,
        a.last_name,
        a.school_name,
        a.major,
        a.acad_role,
        a.city_state,
        a.email,
        u.profile_image,
        u.preferred_pay
    FROM accounts a
    LEFT JOIN userprofile u ON u.user_id = a.id
    WHERE a.id = ?
";
$stmt = $db->prepare($profileSql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$res     = $stmt->get_result();
$profile = $res->fetch_assoc() ?: [];
$stmt->close();

$vImgSrc   = !empty($profile['profile_image']) ? $profile['profile_image'] : 'Images/ProfileIcon.png';
$vFullName = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''));
$vSchool   = $profile['school_name'] ?? '';
$vMajor    = $profile['major']   ?? '';
$vEmail    = $profile['email']   ?? ($userEmail ?: '');

$displayName   = $vFullName !== '' ? $vFullName : $userName;
$displaySchool = $vSchool   !== '' ? $vSchool   : 'Your School';
$displayMajor  = $vMajor    !== '' ? $vMajor    : 'Your Major';

$userName  = $displayName;
$userEmail = $vEmail;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CampusTrade Community Wall</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/FeedPage.css">
</head>
<body>

<!-- MAIN FEED WRAPPER -->
<main class="feed-main-container">
    <div class="feed-container">

        <!-- LEFT SIDEBAR -->
        <aside class="left-sidebar">
            <section class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <img src="<?= htmlspecialchars($vImgSrc) ?>" alt="Profile Picture">
                    </div>
                    <h3><?= htmlspecialchars($displayName) ?></h3>
                    <p><?= htmlspecialchars($displaySchool) ?></p>
                    <p class="user-major"><?= htmlspecialchars($displayMajor) ?></p>
                </div>

                <div class="profile-stats">
                    <div class="stat">
                        <span class="stat-number" id="stat-posts">0</span>
                        <span class="stat-label">Posts</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number" id="stat-events">0</span>
                        <span class="stat-label">Events</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number" id="stat-likes">0</span>
                        <span class="stat-label">Likes Given</span>
                    </div>
                </div>

                <nav class="sidebar-nav">
                    <button class="nav-item" type="button" onclick="window.location.href='SellerPage.php'">
                        <span class="nav-icon">📘</span> Selling
                    </button>
                    <button class="nav-item" type="button" onclick="window.location.href='buyerpage.php'">
                        <span class="nav-icon">🛒</span> Buying
                    </button>
                    <form method="post" action="logout.php" class="logout-form">
                        <button type="submit" class="nav-item logout-btn">
                            <span class="nav-icon">🚪</span> Log Out
                        </button>
                    </form>
                </nav>
            </section>

            <section class="campus-events">
                <h3>Community Rules</h3>
                <div class="event-item">
                    <div class="event-details">
                        <p>
                            • Be respectful and kind.<br>
                            • No harmful or illegal content.<br>
                            • No bullying or harassment.<br>
                            • Keep posts school related.<br>
                            • Do not share private info.<br>
                            • Report unsafe content.
                        </p>
                    </div>
                </div>
            </section>

            <section class="campus-events">
                <h3>Private Chat (Email)</h3>
                <div class="event-item">
                    <div class="event-details">
                        <p>Send a private message through your school email.</p>
                        <form id="private-message-form">
                            <div class="form-group">
                                <input type="email" id="pm-recipient" placeholder="student@school.edu">
                            </div>
                            <div class="form-group">
                                <textarea id="pm-message" rows="2" placeholder="Write a short message..."></textarea>
                            </div>
                            <button type="submit" class="btn-primary">
                                Open in Outlook
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </aside>

        <!-- MAIN FEED (CENTER) -->
        <section class="main-feed">
            <section class="create-post-card">
                <div class="post-input">
                    <img src="<?= htmlspecialchars($vImgSrc) ?>" alt="Your profile">
                    <div class="post-input-main">
                        <p class="posting-as">
                            Posting as <strong><?= htmlspecialchars($userName) ?></strong>
                        </p>
                        <form id="create-post-form">
                            <div class="form-row">
                                <select id="post-type">
                                    <option value="post">Thought / Question</option>
                                    <option value="event">School Event</option>
                                </select>
                            </div>

                            <div class="form-row">
                                <textarea id="post-text"
                                          maxlength="500"
                                          placeholder="Share your thoughts, ask a question, or describe your event..."></textarea>
                                <div class="char-counter">
                                    <span id="char-count">0</span>/500
                                </div>
                            </div>

                            <div id="event-fields" class="form-row" style="display:none;">
                                <label for="event-datetime">Event date &amp; time</label>
                                <input type="datetime-local" id="event-datetime">
                            </div>

                            <div class="form-row form-row-inline">
                                <label class="image-upload">
                                    📷 Add picture
                                    <input type="file" id="post-image" accept="image/*" hidden>
                                </label>
                                <span id="image-file-name" class="image-file-name"></span>
                            </div>

                            <p class="rules-note">
                                Please follow the rules. Do <strong>not</strong> post anything dangerous, illegal, or harmful.
                            </p>

                            <div class="form-row form-row-right">
                                <button type="submit" class="btn-accent">
                                    Post
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <section class="posts-container" id="posts-container"></section>
        </section>

        <!--RIGHT SIDEBAR -->
        <aside class="right-sidebar">
            <section class="trending-section" id="events-sidebar">
                <div class="section-header">
                    <h3>Upcoming Events</h3>
                </div>
                <p class="section-subtitle">
                    These are events posted on the community wall with date and time.
                </p>
                <div id="events-list"></div>
            </section>
        </aside>
    </div>
</main>

<div id="toastContainer" class="toast-container"></div>

<script>
// GLOBAL USER DATA FROM PHP, connects PHP to Javascript
const CURRENT_USER_NAME   = <?= json_encode($userName) ?>;
const CURRENT_USER_EMAIL  = <?= json_encode($userEmail) ?>;
const CURRENT_USER_AVATAR = <?= json_encode($vImgSrc) ?>;

let FEED_POSTS = [];

// Toast helper
function showToast(message) {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    container.appendChild(toast);

    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// JSON fetch
async function fetchJson(url, options = {}) {
    const res  = await fetch(url, options);
    const text = await res.text();

    if (!res.ok) {
        throw new Error('Request failed: ' + res.status + ' ' + text);
    }
    return JSON.parse(text);
}

// Load all posts
async function loadFeed() {
    try {
        const data = await fetchJson('get_posts.php');
        FEED_POSTS = Array.isArray(data) ? data : [];
        renderPosts();
        renderEventsSidebar();
        updateStats();
    } catch (e) {
        console.error(e);
        showToast('Could not load posts.');
    }
}

// Render posts
function renderPosts() {
    const container = document.getElementById('posts-container');
    container.innerHTML = '';

    if (!FEED_POSTS.length) {
        container.innerHTML = '<p class="empty-state">No posts yet. Be the first to share something!</p>';
        return;
    }

    FEED_POSTS.forEach(post => {
        const card = document.createElement('div');
        card.className = 'post-card';
        card.dataset.postId = post.id;

        const createdDate  = new Date(post.createdAt);
        const createdLabel = createdDate.toLocaleString([], { dateStyle: 'short', timeStyle: 'short' });

        let eventMeta = '';
        if (post.type === 'event' && post.eventDateTime) {
            const evDate  = new Date(post.eventDateTime);
            const evLabel = isNaN(evDate)
                ? post.eventDateTime
                : evDate.toLocaleString([], { dateStyle: 'short', timeStyle: 'short' });

            eventMeta = `
                <div class="post-event">
                    <div class="event-details">
                        <h5>📅 Event</h5>
                        <p>${evLabel}</p>
                    </div>
                </div>`;
        }

        const imageHtml = post.imageData
            ? `<div class="post-image">
                   <img src="${post.imageData}" alt="Post image">
               </div>`
            : '';

        const commentsHtml = (post.comments || []).map(c => {
            const cDate  = new Date(c.createdAt);
            const cLabel = isNaN(cDate)
                ? ''
                : cDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            return `
                <div class="comment-item">
                    <strong>${c.author}</strong>
                    <span class="comment-time">${cLabel}</span><br>
                    <span>${c.text}</span>
                </div>`;
        }).join('');

        const isOwner = post.authorEmail && post.authorEmail === CURRENT_USER_EMAIL;
        const deleteButtonHtml = isOwner ? `
            <button class="post-action-btn" type="button" onclick="handleDelete(${post.id})">
                🗑️ Delete
            </button>` : '';

        // Report
        const reportButtonHtml = `
            <button class="post-action-btn" type="button" onclick="handleReport(${post.id})">
                🚩 Report
            </button>`;

        const avatarSrc = post.authorAvatar || 'Images/ProfileIcon.png';

        card.innerHTML = `
            <div class="post-header">
                <img src="${avatarSrc}" alt="User">
                <div class="post-user-info">
                    <h4>${post.author}</h4>
                    <span class="post-meta">${createdLabel}</span>
                </div>
            </div>
            <div class="post-content">
                <p>${post.text}</p>
                ${eventMeta}
                ${imageHtml}
            </div>
            <div class="post-stats">
                <span>👍 ${post.likes} likes</span>
                <span>💬 ${(post.comments || []).length} comments</span>
            </div>
            <div class="post-actions">
                <button class="post-action-btn ${post.liked ? 'liked' : ''}"
                        type="button"
                        onclick="handleLike(${post.id})">
                    👍 ${post.liked ? 'Liked' : 'Like'}
                </button>
                <button class="post-action-btn" type="button" onclick="toggleComments(${post.id})">
                    💬 Comment
                </button>
                ${reportButtonHtml}
                ${deleteButtonHtml}
            </div>
            <div class="comments-section" id="comments-${post.id}" style="display:none;">
                <div class="comments-list">
                    ${commentsHtml || '<p class="no-comments">No comments yet. Start the conversation!</p>'}
                </div>
                <form class="comment-form" onsubmit="return handleCommentSubmit(event, ${post.id});">
                    <input type="text"
                           name="comment"
                           placeholder="Write a comment..."
                           maxlength="200">
                    <button type="submit" class="btn-small">Post</button>
                </form>
            </div>`;
        container.appendChild(card);
    });
}

// Events sidebar
function renderEventsSidebar() {
    const list = document.getElementById('events-list');
    list.innerHTML = '';

    const events = FEED_POSTS.filter(p => p.type === 'event' && p.eventDateTime);
    if (!events.length) {
        list.innerHTML = '<p class="empty-state">No events yet. Post a school event with date and time.</p>';
        return;
    }

    events.slice(0, 6).forEach(ev => {
        const evDiv = document.createElement('div');
        evDiv.className = 'event-item';

        const evDate = new Date(ev.eventDateTime);
        const day    = isNaN(evDate) ? '' : evDate.getDate();
        const month  = isNaN(evDate) ? '' : evDate.toLocaleString('default', { month: 'short' });
        const label  = isNaN(evDate)
            ? ev.eventDateTime
            : evDate.toLocaleString([], { dateStyle: 'short', timeStyle: 'short' });

        evDiv.innerHTML = `
            <div class="event-date">
                <span class="event-day">${day}</span>
                <span class="event-month">${month}</span>
            </div>
            <div class="event-details">
                <h4>${ev.text.substring(0, 60)}${ev.text.length > 60 ? '…' : ''}</h4>
                <p>${label}</p>
            </div>`;
        list.appendChild(evDiv);
    });
}

// Stats
function updateStats() {
    const posts      = FEED_POSTS;
    const events     = posts.filter(p => p.type === 'event');
    const likesGiven = posts.filter(p => p.liked).length;

    const postsEl  = document.getElementById('stat-posts');
    const eventsEl = document.getElementById('stat-events');
    const likesEl  = document.getElementById('stat-likes');

    if (postsEl)  postsEl.textContent  = posts.length;
    if (eventsEl) eventsEl.textContent = events.length;
    if (likesEl)  likesEl.textContent  = likesGiven;
}

// Like / unlike
async function handleLike(id) {
    try {
        await fetchJson('toggle_like.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: id })
        });
        await loadFeed();
    } catch (e) {
        console.error(e);
        showToast('Could not update like.');
    }
}

// Show / hide comments
function toggleComments(id) {
    const section = document.getElementById('comments-' + id);
    if (!section) return;
    section.style.display = (section.style.display === 'none' || !section.style.display)
        ? 'block'
        : 'none';
}

// Add comment
async function handleCommentSubmit(e, id) {
    e.preventDefault();

    const form  = e.target;
    const input = form.querySelector('input[name="comment"]');
    if (!input) return false;

    const text = input.value.trim();
    if (!text) return false;

    try {
        await fetchJson('add_comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: id, text })
        });
        input.value = '';
        await loadFeed();
    } catch (e) {
        console.error(e);
        showToast('Could not add comment.');
    }
    return false;
}

// Delete posts (only your posts)
async function handleDelete(id) {
    const post = FEED_POSTS.find(p => p.id === id);
    if (!post) return;

    if (post.authorEmail && post.authorEmail !== CURRENT_USER_EMAIL) {
        showToast('You can only delete your own posts.');
        return;
    }

    if (!confirm('Are you sure you want to delete this post?')) return;

    try {
        await fetchJson('delete_post.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: id })
        });
        showToast('Post deleted.');
        await loadFeed();
    } catch (e) {
        console.error(e);
        showToast('Could not delete post.');
    }
}

// Report: sneds you to the ContactPage.php
function handleReport(id) {
    const params = new URLSearchParams({
        from: 'feed_report',
        post_id: id
    });
    window.location.href = 'ContactPage.php?' + params.toString();
}

// File
function readFileAsDataURL(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload  = e => resolve(e.target.result);
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}

// Create post form
function setupCreatePostForm() {
    const form            = document.getElementById('create-post-form');
    const typeSelect      = document.getElementById('post-type');
    const eventFields     = document.getElementById('event-fields');
    const textArea        = document.getElementById('post-text');
    const charSpan        = document.getElementById('char-count');
    const imageInput      = document.getElementById('post-image');
    const imageFileName   = document.getElementById('image-file-name');
    const eventDateTimeEl = document.getElementById('event-datetime');

    typeSelect.addEventListener('change', () => {
        eventFields.style.display = (typeSelect.value === 'event') ? 'block' : 'none';
    });

    textArea.addEventListener('input', () => {
        charSpan.textContent = textArea.value.length;
    });

    imageInput.addEventListener('change', () => {
        imageFileName.textContent = imageInput.files && imageInput.files[0]
            ? imageInput.files[0].name
            : '';
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const type          = typeSelect.value;
        const text          = textArea.value.trim();
        const eventDateTime = (type === 'event' && eventDateTimeEl.value) ? eventDateTimeEl.value : null;

        if (!text) {
            showToast('Please write something before posting.');
            return;
        }

        const file = imageInput.files && imageInput.files[0];
        let imageData = null;

        try {
            if (file) {
                imageData = await readFileAsDataURL(file);
            }

            await fetchJson('create_post.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ type, text, imageData, eventDateTime })
            });

            form.reset();
            charSpan.textContent      = '0';
            imageFileName.textContent = '';
            eventFields.style.display = 'none';

            await loadFeed();
            showToast('Post added to the community wall.');
        } catch (e) {
            console.error(e);
            showToast('Could not create post.');
        }
    });
}

// Private message → Outlook
function setupPrivateMessageForm() {
    const form = document.getElementById('private-message-form');
    if (!form) return;

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const toInput  = document.getElementById('pm-recipient');
        const msgInput = document.getElementById('pm-message');

        const to  = (toInput.value || '').trim();
        const msg = (msgInput.value || '').trim();

        if (!to) {
            showToast('Please enter a school email.');
            return;
        }

        const subject    = 'Message from CampusTrade';
        const composeUrl = new URL('https://outlook.office.com/mail/deeplink/compose');
        composeUrl.searchParams.set('to', to);
        composeUrl.searchParams.set('subject', subject);
        composeUrl.searchParams.set('body', msg);

        window.open(composeUrl.toString(), '_blank');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    setupCreatePostForm();
    setupPrivateMessageForm();
    loadFeed();
});
</script>
</body>
</html>
