// Community Discussion - Posts & Replies

async function loadPosts(movieId) {
    try {
        const response = await fetch(`/api/movies/${movieId}/posts`);
        const data = await response.json();
        
        const postsList = document.getElementById('postsList');
        
        if (!data.data || data.data.length === 0) {
            postsList.innerHTML = '<p class="text-muted">No posts yet. Be the first to start the discussion!</p>';
            return;
        }
        
        let html = '';
        for (const post of data.data) {
            html += renderPost(post);
        }
        postsList.innerHTML = html;
        
        // Attach event listeners
        attachPostEventListeners();
    } catch (error) {
        console.error('Error loading posts:', error);
        document.getElementById('postsList').innerHTML = '<p class="text-danger">Failed to load posts.</p>';
    }
}

function renderPost(post) {
    const spoilerBadge = post.spoiler_flag ? '<span class="spoiler-badge">⚠️ SPOILER</span>' : '';
    const bodyClass = post.spoiler_flag ? 'spoiler-hidden' : '';
    const bodyContent = post.spoiler_flag ? '🚨 Click to reveal spoiler' : post.body;
    
    const createdAt = new Date(post.created_at).toLocaleString();
    
    return `
        <div class="post-card" data-post-id="${post.id}">
            <div class="post-header">
                <div>
                    <span class="post-author">📝 ${post.user.name}</span>
                    <span class="post-time">${createdAt}</span>
                </div>
            </div>
            
            <div class="post-title">
                ${post.title}
                ${spoilerBadge}
            </div>
            
            <div class="post-body ${bodyClass}" data-full-body="${post.body}" data-is-spoiler="${post.spoiler_flag}">
                ${bodyContent}
            </div>
            
            <div class="post-actions">
                <button class="post-action-btn reply-btn" data-post-id="${post.id}">💬 Reply</button>
                ${isCurrentUserPost(post) ? `<button class="post-action-btn delete-btn delete-post-btn" data-post-id="${post.id}">🗑️ Delete</button>` : ''}
            </div>
            
            <div id="reply-form-${post.id}" class="reply-form" style="display: none;">
                <textarea class="form-control reply-textarea" placeholder="Write a reply..." maxlength="2000" rows="3"></textarea>
                <div class="form-check mt-2 mb-2">
                    <input type="checkbox" class="form-check-input reply-spoiler-flag" id="replySpoiler-${post.id}">
                    <label class="form-check-label" for="replySpoiler-${post.id}">
                        ⚠️ Contains spoiler
                    </label>
                </div>
                <div class="reply-form-actions">
                    <button class="submit-reply-btn" data-post-id="${post.id}">Post Reply</button>
                    <button class="cancel-btn cancel-reply-btn" data-post-id="${post.id}">Cancel</button>
                </div>
            </div>
            
            <div id="replies-${post.id}" class="replies-container" style="display: none;"></div>
        </div>
    `;
}

function isCurrentUserPost(post) {
    const currentUserId = document.querySelector('meta[name="user-id"]')?.content;
    return currentUserId && currentUserId === post.user_id;
}

function attachPostEventListeners() {
    document.querySelectorAll('.reply-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const replyForm = document.getElementById(`reply-form-${postId}`);
            const repliesContainer = document.getElementById(`replies-${postId}`);
            
            if (replyForm.style.display === 'none') {
                replyForm.style.display = 'block';
                loadReplies(postId);
            } else {
                replyForm.style.display = 'none';
            }
        });
    });
    
    document.querySelectorAll('.post-body.spoiler-hidden').forEach(element => {
        element.addEventListener('click', function() {
            const fullBody = this.dataset.fullBody;
            this.classList.remove('spoiler-hidden');
            this.textContent = fullBody;
        });
    });
    
    document.querySelectorAll('.reply-body.spoiler-hidden').forEach(element => {
        element.addEventListener('click', function() {
            const fullBody = this.dataset.fullBody;
            this.classList.remove('spoiler-hidden');
            this.textContent = fullBody;
        });
    });
    
    document.querySelectorAll('.submit-reply-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const textarea = document.querySelector(`#reply-form-${postId} .reply-textarea`);
            const spoilerFlag = document.querySelector(`#reply-form-${postId} .reply-spoiler-flag`).checked;
            const body = textarea.value.trim();
            
            if (!body) {
                alert('Please write a reply');
                return;
            }
            
            submitReply(postId, body, spoilerFlag);
        });
    });
    
    document.querySelectorAll('.cancel-reply-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const postId = this.dataset.postId;
            document.getElementById(`reply-form-${postId}`).style.display = 'none';
        });
    });
    
    document.querySelectorAll('.delete-post-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const postId = this.dataset.postId;
            if (confirm('Are you sure you want to delete this post?')) {
                deletePost(postId);
            }
        });
    });
}

async function loadReplies(postId) {
    try {
        const response = await fetch(`/api/posts/${postId}/replies`);
        const replies = await response.json();
        
        const repliesContainer = document.getElementById(`replies-${postId}`);
        
        if (!replies || replies.length === 0) {
            repliesContainer.innerHTML = '<p class="text-muted">No replies yet.</p>';
            return;
        }
        
        let html = '<h5 class="mt-3 mb-3" style="color: rgba(255,255,255,0.8);">Replies</h5>';
        for (const reply of replies) {
            html += renderReply(reply);
        }
        
        repliesContainer.innerHTML = html;
        repliesContainer.style.display = 'block';
        
        document.querySelectorAll('.reply-body.spoiler-hidden').forEach(element => {
            element.addEventListener('click', function() {
                const fullBody = this.dataset.fullBody;
                this.classList.remove('spoiler-hidden');
                this.textContent = fullBody;
            });
        });
    } catch (error) {
        console.error('Error loading replies:', error);
    }
}

function renderReply(reply) {
    const spoilerBadge = reply.spoiler_flag ? '<span class="spoiler-badge" style="margin-left: 5px;">⚠️</span>' : '';
    const bodyClass = reply.spoiler_flag ? 'spoiler-hidden' : '';
    const bodyContent = reply.spoiler_flag ? '🚨 Click to reveal spoiler' : reply.body;
    
    const createdAt = new Date(reply.created_at).toLocaleString();
    
    return `
        <div class="reply-card" data-reply-id="${reply.id}">
            <div class="reply-header">
                <div>
                    <span class="reply-author">💬 ${reply.user.name}</span>
                    <span class="reply-time">${createdAt}</span>
                    ${spoilerBadge}
                </div>
                ${isCurrentUserReply(reply) ? `<button class="delete-btn delete-reply-btn" data-reply-id="${reply.id}" style="margin-left: auto;">Delete</button>` : ''}
            </div>
            <div class="reply-body ${bodyClass}" data-full-body="${reply.body}">
                ${bodyContent}
            </div>
        </div>
    `;
}

function isCurrentUserReply(reply) {
    const currentUserId = document.querySelector('meta[name="user-id"]')?.content;
    return currentUserId && currentUserId === reply.user_id;
}

async function createPost(movieId) {
    const title = document.getElementById('postTitle').value.trim();
    const body = document.getElementById('postBody').value.trim();
    const spoilerFlag = document.getElementById('postSpoilerFlag').checked;
    
    if (!title || !body) {
        alert('Please fill in all fields');
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    try {
        const response = await fetch(`/api/movies/${movieId}/posts`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                title: title,
                body: body,
                spoiler_flag: spoilerFlag
            })
        });
        
        if (response.ok) {
            document.getElementById('newPostForm').reset();
            loadPosts(movieId);
        } else {
            alert('Failed to create post');
        }
    } catch (error) {
        console.error('Error creating post:', error);
        alert('An error occurred');
    }
}

async function submitReply(postId, body, spoilerFlag) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    try {
        const response = await fetch(`/api/posts/${postId}/replies`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                body: body,
                spoiler_flag: spoilerFlag
            })
        });
        
        if (response.ok) {
            document.querySelector(`#reply-form-${postId} .reply-textarea`).value = '';
            document.querySelector(`#reply-form-${postId} .reply-spoiler-flag`).checked = false;
            loadReplies(postId);
        } else {
            alert('Failed to submit reply');
        }
    } catch (error) {
        console.error('Error submitting reply:', error);
        alert('An error occurred');
    }
}

async function deletePost(postId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    try {
        const response = await fetch(`/api/posts/${postId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (response.ok) {
            document.querySelector(`[data-post-id="${postId}"]`).remove();
        } else {
            alert('Failed to delete post');
        }
    } catch (error) {
        console.error('Error deleting post:', error);
        alert('An error occurred');
    }
}

// Auto-initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const movieIdElement = document.querySelector('meta[name="movie-id"]');
    if (movieIdElement) {
        const movieId = movieIdElement.content;
        loadPosts(movieId);
        
        const form = document.getElementById('newPostForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                createPost(movieId);
            });
        }
    }
});