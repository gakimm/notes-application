import './bootstrap';

import Alpine from 'alpinejs';

// Alpine.js global stores
Alpine.store('toast', {
    show: false,
    message: '',
    type: 'success',
    
    trigger(message, type = 'success') {
        this.message = message;
        this.type = type;
        this.show = true;
        
        setTimeout(() => {
            this.show = false;
        }, 3000);
    }
});

// Alpine.js global data
Alpine.data('noteEditor', () => ({
    content: '',
    preview: false,
    
    togglePreview() {
        this.preview = !this.preview;
    },
    
    insertText(before, after = '') {
        const textarea = this.$refs.textarea;
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        const selectedText = text.substring(start, end);
        
        const newText = text.substring(0, start) + before + selectedText + after + text.substring(end);
        
        this.content = newText;
        
        this.$nextTick(() => {
            textarea.focus();
            textarea.setSelectionRange(start + before.length, end + before.length);
        });
    },
    
    makeBold() {
        this.insertText('**', '**');
    },
    
    makeItalic() {
        this.insertText('*', '*');
    },
    
    makeHeading() {
        this.insertText('## ');
    },
    
    makeList() {
        this.insertText('- ');
    }
}));

// Alpine.js comment component
Alpine.data('commentBox', () => ({
    open: false,
    content: '',
    loading: false,
    
    async submitComment() {
        if (!this.content.trim()) return;
        
        this.loading = true;
        
        try {
            const response = await fetch(window.location.href + '/comments', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    content: this.content
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.content = '';
                this.open = false;
                Alpine.store('toast').trigger(data.message);
                // Refresh comments (emit event)
                this.$dispatch('comment-added', data.comment);
            }
        } catch (error) {
            Alpine.store('toast').trigger('Terjadi error saat menambah comment', 'error');
        } finally {
            this.loading = false;
        }
    }
}));

window.Alpine = Alpine;

Alpine.start();