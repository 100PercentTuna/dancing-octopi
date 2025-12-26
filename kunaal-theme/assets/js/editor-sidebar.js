/**
 * Kunaal Theme - Gutenberg Sidebar Plugin
 * Includes Essay/Jotting Details with Topics selection in same panel
 * Auto-calculates read time based on content
 */
(function(wp) {
    const { registerPlugin } = wp.plugins;
    const { PluginDocumentSettingPanel } = wp.editPost;
    const { TextControl, TextareaControl, Button, Spinner, CheckboxControl, PanelRow } = wp.components;
    const { useSelect, useDispatch } = wp.data;
    const { useState, useEffect, useCallback } = wp.element;
    const { MediaUpload, MediaUploadCheck } = wp.blockEditor;

    // Helper: Calculate read time from content
    const calculateReadTime = (content) => {
        if (!content) return 0;
        // Strip HTML tags
        const text = content.replace(/<[^>]*>/g, '');
        // Count words (average reading speed: 200 words per minute)
        const words = text.trim().split(/\s+/).filter(w => w.length > 0).length;
        const minutes = Math.ceil(words / 200);
        return Math.max(1, minutes);
    };

    // Essay Details Panel Component
    const EssayDetailsPanel = () => {
        const postType = useSelect(select => select('core/editor').getCurrentPostType());
        
        if (postType !== 'essay') {
            return null;
        }

        const { editPost } = useDispatch('core/editor');
        
        const meta = useSelect(select => select('core/editor').getEditedPostAttribute('meta') || {});
        const content = useSelect(select => select('core/editor').getEditedPostAttribute('content') || '');
        const postTerms = useSelect(select => select('core/editor').getEditedPostAttribute('topic') || []);
        
        const subtitle = meta.kunaal_subtitle || '';
        const readTime = meta.kunaal_read_time || '';
        const cardImageId = meta.kunaal_card_image || 0;
        const summary = meta.kunaal_summary || '';

        const [imageUrl, setImageUrl] = useState('');
        const [imageLoading, setImageLoading] = useState(false);
        const [allTopics, setAllTopics] = useState([]);
        const [selectedTopics, setSelectedTopics] = useState(postTerms);
        const [autoReadTime, setAutoReadTime] = useState(0);
        const [newTopicName, setNewTopicName] = useState('');
        const [isCreatingTopic, setIsCreatingTopic] = useState(false);

        // Load all topics
        useEffect(() => {
            wp.apiFetch({ path: '/wp/v2/topic?per_page=100' })
                .then(topics => {
                    setAllTopics(topics.map(t => ({ id: t.id, name: t.name, slug: t.slug })));
                })
                .catch(() => setAllTopics([]));
        }, []);

        // Sync selected topics from post
        useEffect(() => {
            setSelectedTopics(postTerms);
        }, [postTerms]);

        // Auto-calculate read time
        useEffect(() => {
            const calculated = calculateReadTime(content);
            setAutoReadTime(calculated);
            // Auto-update if read time is empty
            if (!readTime && calculated > 0) {
                editPost({ meta: { ...meta, kunaal_read_time: calculated } });
            }
        }, [content]);

        // Load card image URL
        useEffect(() => {
            if (cardImageId) {
                setImageLoading(true);
                wp.apiFetch({ path: `/wp/v2/media/${cardImageId}` })
                    .then(media => {
                        const url = media.media_details?.sizes?.medium?.source_url || media.source_url;
                        setImageUrl(url);
                        setImageLoading(false);
                    })
                    .catch(() => {
                        setImageUrl('');
                        setImageLoading(false);
                    });
            } else {
                setImageUrl('');
            }
        }, [cardImageId]);

        const updateMeta = (key, value) => {
            editPost({ meta: { ...meta, [key]: value } });
        };

        const toggleTopic = (topicId) => {
            let newTopics;
            if (selectedTopics.includes(topicId)) {
                newTopics = selectedTopics.filter(id => id !== topicId);
            } else {
                newTopics = [...selectedTopics, topicId];
            }
            setSelectedTopics(newTopics);
            editPost({ topic: newTopics });
        };

        const useAutoReadTime = () => {
            updateMeta('kunaal_read_time', autoReadTime);
        };

        const createNewTopic = async () => {
            if (!newTopicName.trim()) return;
            
            setIsCreatingTopic(true);
            try {
                const newTopic = await wp.apiFetch({
                    path: '/wp/v2/topic',
                    method: 'POST',
                    data: {
                        name: newTopicName.trim(),
                        slug: newTopicName.trim().toLowerCase().replace(/\s+/g, '-')
                    }
                });
                
                // Add new topic to list
                setAllTopics([...allTopics, { id: newTopic.id, name: newTopic.name, slug: newTopic.slug }]);
                // Auto-select the new topic
                const newSelected = [...selectedTopics, newTopic.id];
                setSelectedTopics(newSelected);
                editPost({ topic: newSelected });
                // Clear input
                setNewTopicName('');
            } catch (error) {
                console.error('Failed to create topic:', error);
                // Error handled by UI feedback, no alert needed
                console.error('Failed to create topic');
            } finally {
                setIsCreatingTopic(false);
            }
        };

        return wp.element.createElement(
            wp.element.Fragment,
            null,
            // Essay Details Panel
            wp.element.createElement(
                PluginDocumentSettingPanel,
                {
                    name: 'kunaal-essay-details',
                    title: '📝 Essay Details',
                    className: 'kunaal-essay-details-panel',
                    initialOpen: true
                },
                wp.element.createElement(TextControl, {
                    label: 'Subtitle / Dek *',
                    value: subtitle,
                    onChange: (value) => updateMeta('kunaal_subtitle', value),
                    help: 'Required. Appears below the title.',
                    className: subtitle ? '' : 'kunaal-field-missing'
                }),
                wp.element.createElement(
                    'div',
                    { style: { marginBottom: '16px' } },
                    wp.element.createElement(
                        'label',
                        { style: { display: 'block', marginBottom: '4px', fontWeight: '500' } },
                        'Read Time (minutes) *'
                    ),
                    wp.element.createElement(
                        'div',
                        { style: { display: 'flex', gap: '8px', alignItems: 'center' } },
                        wp.element.createElement('input', {
                            type: 'number',
                            min: 1,
                            max: 120,
                            value: readTime,
                            onChange: (e) => updateMeta('kunaal_read_time', parseInt(e.target.value) || ''),
                            style: { width: '80px', padding: '6px 8px' },
                            className: readTime ? '' : 'kunaal-field-missing'
                        }),
                        wp.element.createElement(
                            Button,
                            {
                                variant: 'secondary',
                                isSmall: true,
                                onClick: useAutoReadTime,
                                title: 'Auto-calculate based on content'
                            },
                            '⚡ Auto (' + autoReadTime + ' min)'
                        )
                    ),
                    wp.element.createElement(
                        'p',
                        { style: { fontSize: '12px', color: '#666', marginTop: '4px' } },
                        'Click Auto to calculate from content (~200 words/min)'
                    )
                ),
                wp.element.createElement(TextareaControl, {
                    label: 'Summary for Email Subscribers',
                    value: summary,
                    onChange: (value) => updateMeta('kunaal_summary', value),
                    help: 'Brief summary for notification emails. Supports basic formatting.',
                    rows: 3
                })
            ),
            // Topics Panel (IN SIDEBAR)
            wp.element.createElement(
                PluginDocumentSettingPanel,
                {
                    name: 'kunaal-topics',
                    title: '🏷️ Topics *',
                    className: 'kunaal-topics-panel',
                    initialOpen: true
                },
                allTopics.length === 0 
                    ? wp.element.createElement(Spinner, null)
                    : wp.element.createElement(
                        'div',
                        null,
                        wp.element.createElement(
                            'div',
                            { style: { maxHeight: '200px', overflowY: 'auto', marginBottom: '12px' } },
                            allTopics.map(topic => 
                                wp.element.createElement(CheckboxControl, {
                                    key: topic.id,
                                    label: '#' + topic.name,
                                    checked: selectedTopics.includes(topic.id),
                                    onChange: () => toggleTopic(topic.id)
                                })
                            )
                        ),
                        wp.element.createElement(
                            'div',
                            { style: { borderTop: '1px solid #ddd', paddingTop: '12px', marginTop: '12px' } },
                            wp.element.createElement(
                                'label',
                                { style: { display: 'block', marginBottom: '4px', fontSize: '12px', fontWeight: '500' } },
                                'Add New Topic'
                            ),
                            wp.element.createElement(
                                'div',
                                { style: { display: 'flex', gap: '8px' } },
                                wp.element.createElement('input', {
                                    type: 'text',
                                    value: newTopicName,
                                    onChange: (e) => setNewTopicName(e.target.value),
                                    placeholder: 'New topic name',
                                    style: { flex: 1, padding: '6px 8px', fontSize: '13px' },
                                    onKeyPress: (e) => { if (e.key === 'Enter') createNewTopic(); }
                                }),
                                wp.element.createElement(
                                    Button,
                                    {
                                        variant: 'primary',
                                        isSmall: true,
                                        onClick: createNewTopic,
                                        disabled: !newTopicName.trim() || isCreatingTopic,
                                        isBusy: isCreatingTopic
                                    },
                                    '+ Add'
                                )
                            )
                        )
                    ),
                wp.element.createElement(
                    'p',
                    { 
                        style: { 
                            fontSize: '12px', 
                            color: selectedTopics.length === 0 ? '#d63638' : '#666',
                            marginTop: '8px',
                            fontWeight: selectedTopics.length === 0 ? '500' : 'normal'
                        } 
                    },
                    selectedTopics.length === 0 
                        ? '⚠️ At least one topic required to publish'
                        : selectedTopics.length + ' topic(s) selected'
                )
            ),
            // Card Image Panel
            wp.element.createElement(
                PluginDocumentSettingPanel,
                {
                    name: 'kunaal-card-image',
                    title: '🖼️ Card Image *',
                    className: 'kunaal-card-image-panel'
                },
                wp.element.createElement(
                    MediaUploadCheck,
                    null,
                    wp.element.createElement(MediaUpload, {
                        onSelect: (media) => updateMeta('kunaal_card_image', media.id),
                        allowedTypes: ['image'],
                        value: cardImageId,
                        render: ({ open }) => wp.element.createElement(
                            'div',
                            null,
                            imageLoading && wp.element.createElement(Spinner, null),
                            imageUrl && !imageLoading && wp.element.createElement(
                                'div',
                                { style: { marginBottom: '10px' } },
                                wp.element.createElement('img', {
                                    src: imageUrl,
                                    alt: 'Card image',
                                    style: { maxWidth: '100%', height: 'auto', borderRadius: '4px' }
                                })
                            ),
                            wp.element.createElement(
                                'div',
                                { style: { display: 'flex', gap: '8px' } },
                                wp.element.createElement(
                                    Button,
                                    {
                                        onClick: open,
                                        variant: cardImageId ? 'secondary' : 'primary'
                                    },
                                    cardImageId ? 'Replace' : 'Select Image *'
                                ),
                                cardImageId && wp.element.createElement(
                                    Button,
                                    {
                                        onClick: () => updateMeta('kunaal_card_image', 0),
                                        variant: 'tertiary',
                                        isDestructive: true
                                    },
                                    'Remove'
                                )
                            )
                        )
                    })
                ),
                wp.element.createElement(
                    'p',
                    { style: { fontSize: '12px', color: '#666', marginTop: '8px' } },
                    'Recommended: 4:5 ratio (800×1000px)'
                )
            )
        );
    };

    // Jotting Details Panel Component  
    const JottingDetailsPanel = () => {
        const postType = useSelect(select => select('core/editor').getCurrentPostType());
        
        if (postType !== 'jotting') {
            return null;
        }

        const { editPost } = useDispatch('core/editor');
        const meta = useSelect(select => select('core/editor').getEditedPostAttribute('meta') || {});
        const postTerms = useSelect(select => select('core/editor').getEditedPostAttribute('topic') || []);
        
        const subtitle = meta.kunaal_subtitle || '';
        const summary = meta.kunaal_summary || '';
        
        const [allTopics, setAllTopics] = useState([]);
        const [selectedTopics, setSelectedTopics] = useState(postTerms);

        // Load all topics
        useEffect(() => {
            wp.apiFetch({ path: '/wp/v2/topic?per_page=100' })
                .then(topics => {
                    setAllTopics(topics.map(t => ({ id: t.id, name: t.name, slug: t.slug })));
                })
                .catch(() => setAllTopics([]));
        }, []);

        useEffect(() => {
            setSelectedTopics(postTerms);
        }, [postTerms]);

        const toggleTopic = (topicId) => {
            let newTopics;
            if (selectedTopics.includes(topicId)) {
                newTopics = selectedTopics.filter(id => id !== topicId);
            } else {
                newTopics = [...selectedTopics, topicId];
            }
            setSelectedTopics(newTopics);
            editPost({ topic: newTopics });
        };

        return wp.element.createElement(
            wp.element.Fragment,
            null,
            wp.element.createElement(
                PluginDocumentSettingPanel,
                {
                    name: 'kunaal-jotting-details',
                    title: '📝 Jotting Details',
                    className: 'kunaal-jotting-details-panel',
                    initialOpen: true
                },
                wp.element.createElement(TextControl, {
                    label: 'Subtitle / Dek *',
                    value: subtitle,
                    onChange: (value) => editPost({ meta: { ...meta, kunaal_subtitle: value } }),
                    help: 'Required. Short description shown in the list.',
                    className: subtitle ? '' : 'kunaal-field-missing'
                }),
                wp.element.createElement(TextareaControl, {
                    label: 'Summary for Email Subscribers',
                    value: summary,
                    onChange: (value) => editPost({ meta: { ...meta, kunaal_summary: value } }),
                    help: 'Brief summary for notification emails.',
                    rows: 3
                })
            ),
            // Topics Panel for Jottings
            wp.element.createElement(
                PluginDocumentSettingPanel,
                {
                    name: 'kunaal-jotting-topics',
                    title: '🏷️ Topics',
                    className: 'kunaal-topics-panel',
                    initialOpen: true
                },
                allTopics.length === 0 
                    ? wp.element.createElement(Spinner, null)
                    : wp.element.createElement(
                        'div',
                        { style: { maxHeight: '200px', overflowY: 'auto' } },
                        allTopics.map(topic => 
                            wp.element.createElement(CheckboxControl, {
                                key: topic.id,
                                label: '#' + topic.name,
                                checked: selectedTopics.includes(topic.id),
                                onChange: () => toggleTopic(topic.id)
                            })
                        )
                    ),
                wp.element.createElement(
                    'p',
                    { style: { fontSize: '12px', color: '#666', marginTop: '8px' } },
                    selectedTopics.length + ' topic(s) selected'
                )
            )
        );
    };

    // Register the plugins
    registerPlugin('kunaal-essay-sidebar', {
        render: EssayDetailsPanel,
        icon: null
    });

    registerPlugin('kunaal-jotting-sidebar', {
        render: JottingDetailsPanel,
        icon: null
    });

})(window.wp);
