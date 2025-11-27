<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: LoginPage.php');
    exit;
}
include('header.php');


$userName = isset($_SESSION['firstName']) && $_SESSION['firstName'] !== ''
    ? $_SESSION['firstName']
    : (isset($_SESSION['email']) ? $_SESSION['email'] : 'Student User');

$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : '';


$db = require __DIR__ . '/Database.php';

$userId = (int) $_SESSION['user_id'];

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


$vImgSrc    = !empty($profile['profile_image']) ? $profile['profile_image'] : 'Images/ProfileIcon.png';
$vFullName  = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''));
$vSchool    = $profile['school_name'] ?? '';
$vMajor     = $profile['major'] ?? '';
$vAcad      = $profile['acad_role'] ?? '';
$vCityState = $profile['city_state'] ?? '';
$vEmail     = $profile['email'] ?? ($userEmail ?: '');
$vPay       = $profile['preferred_pay'] ?? 'Cash';


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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusTrade Community Wall</title>
    <link rel="stylesheet" href="CSS/FeedPage.css">
</head>
<body>
<main class="feed-main-container">
    <div class="feed-container">

        <!-- LEFT SIDEBAR  -->
        <div class="left-sidebar">
            <div class="profile-card">
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

                <div class="sidebar-nav">
                    <button class="nav-item" type="button" onclick="window.location.href='SellerPage.php'">
                        <span class="nav-icon">📘</span>
                        Selling
                    </button>
                    <button class="nav-item" type="button" onclick="window.location.href='buyerpage.php'">
                        <span class="nav-icon">🛒</span>
                        Buying
                    </button>
                    <form method="post" action="logout.php" class="logout-form">
                        <button type="submit" class="nav-item logout-btn">
                            <span class="nav-icon">🚪</span>
                            Log Out
                        </button>
                    </form>
                </div>
            </div>

            <div class="campus-events">
                <h3>Community Rules</h3>
                <div class="event-item">
                    <div class="event-details">
                        <p style="margin:0; font-size:1.05rem; line-height:1.6;">
                            • Be respectful and kind.<br>
                            • No harmful or illegal content.<br>
                            • No bullying or harassment.<br>
                            • Keep posts school related.<br>
                            • Do not share private info.<br>
                            • Report unsafe content.
                        </p>
                    </div>
                </div>
            </div>

            <div class="campus-events">
                <h3>Private Chat (Email)</h3>
                <div class="event-item" style="align-items:flex-start;">
                    <div class="event-details" style="width:100%;">
                        <p style="margin-top:0; font-size:1rem;">
                            Send a private message through your school email.
                        </p>
                        <form id="private-message-form">
                            <div class="form-group" style="margin-bottom:8px;">
                                <input type="email" id="pm-recipient" placeholder="student@school.edu">
                            </div>
                            <div class="form-group" style="margin-bottom:8px;">
                                <textarea id="pm-message" rows="2" placeholder="Write a short message..."></textarea>
                            </div>
                            <button type="submit"
                                    style="background:#003748;color:#fff;border:none;padding:10px 18px;border-radius:10px;cursor:pointer;font-size:1rem;">
                                Open in Outlook
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN FEED  -->
        <div class="main-feed">
            <div class="create-post-card">
                <div class="post-input" style="align-items:flex-start;">

                    <img src="<?= htmlspecialchars($vImgSrc) ?>" alt="Your profile">
                    <div style="flex:1;">
                        <p style="margin:0 0 10px; color:#666; font-size:1rem;">
                            Posting as <strong><?php echo htmlspecialchars($userName); ?></strong>
                        </p>
                        <form id="create-post-form">
                            <div style="margin-bottom:10px;">
                                <select id="post-type"
                                        style="padding:8px 10px; border-radius:10px; border:1px solid #e4e6eb; font-size:1rem;">
                                    <option value="post">Thought / Question</option>
                                    <option value="event">School Event</option>
                                </select>
                            </div>

                            <div style="margin-bottom:10px;">
                                <textarea id="post-text"
                                          maxlength="500"
                                          placeholder="Share your thoughts, ask a question, or describe your event..."
                                          style="width:100%; padding:14px 16px; border-radius:14px; border:2px solid #e4e6eb; resize:vertical; min-height:140px; font-size:1.15rem;"></textarea>
                                <div style="text-align:right; font-size:0.95rem; color:#888; margin-top:6px;">
                                    <span id="char-count">0</span>/500
                                </div>
                            </div>

                            <div id="event-fields" style="display:none; margin-bottom:12px;">
                                <label style="font-size:0.95rem; color:#555; display:block; margin-bottom:4px;">
                                    Event date &amp; time
                                </label>
                                <input type="datetime-local"
                                       id="event-datetime"
                                       style="width:100%; padding:10px 12px; border-radius:10px; border:1px solid #e4e6eb;">
                            </div>

                            <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                                <label class="image-upload"
                                       style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; font-size:1.05rem; color:#003748;">
                                    📷 Add picture
                                    <input type="file" id="post-image" accept="image/*" style="display:none;">
                                </label>
                                <span id="image-file-name" class="image-file-name" style="font-size:0.95rem; color:#666;"></span>
                            </div>

                            <p style="font-size:0.9rem; color:#666; margin-bottom:10px;">
                                Please follow the rules. Do <strong>not</strong> post anything dangerous, illegal, or harmful.
                            </p>

                            <div style="text-align:right;">
                                <button type="submit"
                                        style="background:#FF8351; color:#fff; border:none; padding:10px 22px; border-radius:12px; cursor:pointer; font-weight:600; font-size:1.1rem;">
                                    Post
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="posts-container" id="posts-container"></div>
        </div>


        <div class="right-sidebar">
            <div class="trending-section" id="events-sidebar">
                <div class="section-header" style="margin-bottom:12px;">
                    <h3>Upcoming Events</h3>
                </div>
                <p style="font-size:0.95rem; color:#666; margin-top:0; margin-bottom:10px;">
                    These are events posted on the community wall with date and time.
                </p>
                <div id="events-list"></div>
            </div>
        </div>
    </div>
</main>

<div id="toastContainer" class="toast-container"></div>

<script>
const CURRENT_USER_NAME   = <?php echo json_encode($userName); ?>;
const CURRENT_USER_EMAIL  = <?php echo json_encode($userEmail); ?>;
const CURRENT_USER_AVATAR = <?php echo json_encode($vImgSrc); ?>;

function showToast(message) {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 50);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

const FeedStore = (function () {
    const STORAGE_KEY = 'campustrade_feed_v2';
    let posts = [];

    function load() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            let data = raw ? JSON.parse(raw) : [];
            if (!Array.isArray(data)) {
                data = [];
            } else if (data.length > 0 && typeof data[0].authorEmail === 'undefined') {
                data = [];
            }
            posts = data;
        } catch (e) {
            posts = [];
        }
        posts.sort((a, b) => b.createdAt - a.createdAt);
        save();
    }

    function save() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(posts));
    }

    function createPost({ type, text, imageData, eventDateTime }) {
        const now = Date.now();
        const post = {
            id: now,
            type: type,
            text: text.trim(),
            imageData: imageData || null,
            eventDateTime: eventDateTime || null,
            author: CURRENT_USER_NAME || 'Student User',
            authorEmail: CURRENT_USER_EMAIL || '',
            authorAvatar: CURRENT_USER_AVATAR || 'Images/ProfileIcon.png',
            createdAt: now,
            likes: 0,
            liked: false,
            comments: []
        };
        posts.unshift(post);
        save();
        return post;
    }

    function toggleLike(id) {
        const idx = posts.findIndex(p => p.id === id);
        if (idx === -1) return null;
        const post = posts[idx];
        post.liked = !post.liked;
        post.likes += post.liked ? 1 : -1;
        if (post.likes < 0) post.likes = 0;
        save();
        return post;
    }

    function addComment(id, text) {
        const idx = posts.findIndex(p => p.id === id);
        if (idx === -1) return null;
        const trimmed = text.trim();
        if (!trimmed) return null;
        posts[idx].comments.push({
            author: CURRENT_USER_NAME || 'Student',
            text: trimmed,
            createdAt: Date.now()
        });
        save();
        return posts[idx];
    }

    function deletePost(id) {
        const idx = posts.findIndex(p => p.id === id);
        if (idx === -1) return false;
        posts.splice(idx, 1);
        save();
        return true;
    }

    function getPosts() {
        return posts.slice();
    }

    function getEventPosts() {
        return posts
            .filter(p => p.type === 'event' && p.eventDateTime)
            .slice()
            .sort((a, b) => new Date(a.eventDateTime) - new Date(b.eventDateTime));
    }

    function getMyEventCount() {
        return posts.filter(p => p.type === 'event' && p.author === CURRENT_USER_NAME).length;
    }

    function getLikesGivenCount() {
        return posts.filter(p => p.liked).length;
    }

    return {
        load,
        save,
        createPost,
        toggleLike,
        addComment,
        deletePost,
        getPosts,
        getEventPosts,
        getMyEventCount,
        getLikesGivenCount
    };
})();

function renderPosts() {
    const container = document.getElementById('posts-container');
    container.innerHTML = '';
    const posts = FeedStore.getPosts();

    if (!posts.length) {
        container.innerHTML = '<p class="empty-state" style="color:#666;">No posts yet. Be the first to share something!</p>';
        return;
    }

    posts.forEach(post => {
        const card = document.createElement('div');
        card.className = 'post-card';
        card.dataset.postId = post.id;

        const createdDate = new Date(post.createdAt);
        const createdLabel = createdDate.toLocaleString([], { dateStyle: 'short', timeStyle: 'short' });

        let eventMeta = '';
        if (post.type === 'event' && post.eventDateTime) {
            const evDate = new Date(post.eventDateTime);
            const evLabel = isNaN(evDate)
                ? post.eventDateTime
                : evDate.toLocaleString([], { dateStyle: 'short', timeStyle: 'short' });
            eventMeta = `
                <div class="study-event" style="margin-top:10px;">
                    <div class="event-details">
                        <h5>📅 Event</h5>
                        <p>${evLabel}</p>
                    </div>
                </div>
            `;
        }

        const imageHtml = post.imageData
            ? `<div class="post-image" style="margin-top:10px;">
                    <img src="${post.imageData}" alt="Post image" style="max-width:100%; border-radius:12px;">
               </div>`
            : '';

        const commentsHtml = post.comments.map(c => {
            const cDate = new Date(c.createdAt);
            const cLabel = cDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            return `<div class="comment-item" style="margin-bottom:6px;">
                        <strong>${c.author}</strong>
                        <span style="font-size:0.8rem; color:#888; margin-left:6px;">${cLabel}</span><br>
                        <span>${c.text}</span>
                    </div>`;
        }).join('');

        const isOwner = post.authorEmail && post.authorEmail === CURRENT_USER_EMAIL;

        const deleteButtonHtml = isOwner ? `
            <button class="post-action-btn"
                    type="button"
                    onclick="handleDelete(${post.id})">
                <span class="action-icon">🗑️</span>
                <span class="action-text">Delete</span>
            </button>
        ` : '';

        const reportButtonHtml = `
            <button class="post-action-btn"
                    type="button"
                    onclick="handleReport(${post.id})">
                <span class="action-icon">🚩</span>
                <span class="action-text">Report</span>
            </button>
        `;

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
                <span class="likes-count">👍 ${post.likes} likes</span>
                <span class="comments-count">💬 ${post.comments.length} comments</span>
            </div>
            <div class="post-actions">
                <button class="post-action-btn ${post.liked ? 'liked' : ''}"
                        type="button"
                        onclick="handleLike(${post.id})">
                    <span class="action-icon">👍</span>
                    <span class="action-text">${post.liked ? 'Liked' : 'Like'}</span>
                </button>
                <button class="post-action-btn" type="button"
                        onclick="toggleComments(${post.id})">
                    <span class="action-icon">💬</span>
                    <span class="action-text">Comment</span>
                </button>
                ${reportButtonHtml}
                ${deleteButtonHtml}
            </div>
            <div class="comments-section" id="comments-${post.id}" style="margin-top:10px; display:none;">
                <div class="comments-list" style="margin-bottom:8px;">
                    ${commentsHtml || '<p class="no-comments" style="color:#888; font-size:0.9rem;">No comments yet. Start the conversation!</p>'}
                </div>
                <form class="comment-form" onsubmit="return handleCommentSubmit(event, ${post.id});" style="display:flex; gap:8px;">
                    <input type="text"
                           name="comment"
                           placeholder="Write a comment..."
                           maxlength="200"
                           style="flex:1; padding:8px 10px; border-radius:8px; border:1px solid #e4e6eb;">
                    <button type="submit"
                            style="background:#003748; color:#fff; border:none; padding:8px 14px; border-radius:8px; cursor:pointer; font-size:0.9rem;">
                        Post
                    </button>
                </form>
            </div>
        `;
        container.appendChild(card);
    });
}

function renderEventsSidebar() {
    const list = document.getElementById('events-list');
    if (!list) return;
    list.innerHTML = '';

    const events = FeedStore.getEventPosts();
    if (!events.length) {
        list.innerHTML = '<p style="font-size:0.95rem; color:#666;">No events yet. Post a school event with date and time.</p>';
        return;
    }

    events.slice(0, 6).forEach(ev => {
        const evDiv = document.createElement('div');
        evDiv.className = 'event-item';

        const evDate = new Date(ev.eventDateTime);
        const day = isNaN(evDate) ? '' : evDate.getDate();
        const month = isNaN(evDate) ? '' : evDate.toLocaleString('default', { month: 'short' });
        const label = isNaN(evDate)
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
            </div>
        `;
        list.appendChild(evDiv);
    });
}

function updateStats() {
    const posts = FeedStore.getPosts();
    const events = FeedStore.getEventPosts();
    const likesGiven = FeedStore.getLikesGivenCount();

    const postsEl  = document.getElementById('stat-posts');
    const eventsEl = document.getElementById('stat-events');
    const likesEl  = document.getElementById('stat-likes');

    if (postsEl)  postsEl.textContent  = posts.length;
    if (eventsEl) eventsEl.textContent = events.length;
    if (likesEl)  likesEl.textContent  = likesGiven;
}

function handleLike(id) {
    const post = FeedStore.toggleLike(id);
    if (!post) return;
    renderPosts();
    updateStats();
}

function toggleComments(id) {
    const section = document.getElementById('comments-' + id);
    if (!section) return;
    section.style.display = (section.style.display === 'none' || !section.style.display) ? 'block' : 'none';
}

function handleCommentSubmit(e, id) {
    e.preventDefault();
    const form = e.target;
    const input = form.querySelector('input[name="comment"]');
    if (!input) return false;
    const text = input.value.trim();
    if (!text) return false;

    FeedStore.addComment(id, text);
    input.value = '';
    renderPosts();
    return false;
}

function handleDelete(id) {
    const posts = FeedStore.getPosts();
    const post = posts.find(p => p.id === id);
    if (!post) return;

    if (post.authorEmail && post.authorEmail !== CURRENT_USER_EMAIL) {
        showToast("You can only delete your own posts.");
        return;
    }

    if (!confirm('Are you sure you want to delete this post?')) return;

    if (FeedStore.deletePost(id)) {
        showToast('Post deleted.');
        renderPosts();
        renderEventsSidebar();
        updateStats();
    }
}


function handleReport(id) {
    const reason = prompt("Please briefly describe why you are reporting this post (e.g., harassment, unsafe content):");
    if (!reason) return;

    const params = new URLSearchParams({
        from: 'feed_report',
        post_id: id,
        reason: reason
    });

    window.location.href = 'ContactPage.php?' + params.toString();
}

function setupCreatePostForm() {
    const form = document.getElementById('create-post-form');
    const typeSelect = document.getElementById('post-type');
    const eventFields = document.getElementById('event-fields');
    const textArea = document.getElementById('post-text');
    const charSpan = document.getElementById('char-count');
    const imageInput = document.getElementById('post-image');
    const imageFileName = document.getElementById('image-file-name');

    typeSelect.addEventListener('change', () => {
        if (typeSelect.value === 'event') {
            eventFields.style.display = 'block';
        } else {
            eventFields.style.display = 'none';
        }
    });

    textArea.addEventListener('input', () => {
        const len = textArea.value.length;
        charSpan.textContent = len;
    });

    imageInput.addEventListener('change', () => {
        if (imageInput.files && imageInput.files[0]) {
            imageFileName.textContent = imageInput.files[0].name;
        } else {
            imageFileName.textContent = '';
        }
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const type = typeSelect.value;
        const text = textArea.value.trim();
        const eventDateTimeInput = document.getElementById('event-datetime');
        const eventDateTime = (type === 'event' && eventDateTimeInput.value) ? eventDateTimeInput.value : null;

        if (!text) {
            showToast('Please write something before posting.');
            return;
        }

        const file = imageInput.files && imageInput.files[0];

        const finishPost = (imageData) => {
            FeedStore.createPost({ type, text, imageData, eventDateTime });
            form.reset();
            charSpan.textContent = '0';
            imageFileName.textContent = '';
            eventFields.style.display = 'none';
            renderPosts();
            renderEventsSidebar();
            updateStats();
            showToast('Post added to the community wall.');
        };

        if (file) {
            const reader = new FileReader();
            reader.onload = function (evt) {
                finishPost(evt.target.result);
            };
            reader.readAsDataURL(file);
        } else {
            finishPost(null);
        }
    });
}

function setupPrivateMessageForm() {
    const form = document.getElementById('private-message-form');
    if (!form) return;

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const toInput = document.getElementById('pm-recipient');
        const msgInput = document.getElementById('pm-message');

        const to = (toInput.value || '').trim();
        const msg = (msgInput.value || '').trim();

        if (!to) {
            showToast('Please enter a school email.');
            return;
        }

        const subject = 'Message from CampusTrade';
        const composeUrl = new URL('https://outlook.office.com/mail/deeplink/compose');
        composeUrl.searchParams.set('to', to);
        composeUrl.searchParams.set('subject', subject);
        composeUrl.searchParams.set('body', msg);
        window.open(composeUrl.toString(), '_blank');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    FeedStore.load();
    setupCreatePostForm();
    setupPrivateMessageForm();
    renderPosts();
    renderEventsSidebar();
    updateStats();
});
</script>
</body>
</html>
