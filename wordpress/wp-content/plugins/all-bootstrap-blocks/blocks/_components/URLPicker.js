function URLPicker( {
    areoi,
    isSelected,
    url,
    urlTitle,
    setAttributes,
    opensInNewTab,
    onToggleOpenInNewTab,
    anchorRef,
    richTextRef,
} ) {
    const [ isEditingURL, setIsEditingURL ] = areoi.element.useState( false );
    const isURLSet = !! url;

    const startEditing = ( event ) => {
        event.preventDefault();
        setIsEditingURL( true );
    };

    const unlink = () => {
        setAttributes( {
            url: undefined,
            linkTarget: undefined,
            rel: undefined,
        } );
        setIsEditingURL( false );
    };

    areoi.element.useEffect( () => {
        if ( ! isSelected ) {
            setIsEditingURL( false );
        }
    }, [ isSelected ] );

    const isLinkControlVisible = isSelected && ( isEditingURL || isURLSet );
    
    const linkControl = isLinkControlVisible && (
        <areoi.components.Popover
            position="bottom center"
            onClose={ () => {
                setIsEditingURL( false );
                richTextRef.current?.focus();
            } }
            anchorRef={ anchorRef?.current }
            focusOnMount={ isEditingURL ? 'firstElement' : false }
        >
            <areoi.editor.__experimentalLinkControl
                className="wp-block-navigation-link__inline-link-input"
                value={ { url, opensInNewTab, urlTitle } }
                onChange={ ( {
                    url: newURL = '',
                    opensInNewTab: newOpensInNewTab,
                    urlTitle: newTitle = ''
                } ) => {
                    setAttributes( { url: newURL, url_title: newTitle } );

                    if ( opensInNewTab !== newOpensInNewTab ) {
                        onToggleOpenInNewTab( newOpensInNewTab );
                    }
                } }
                onRemove={ () => {
                    unlink();
                    richTextRef.current?.focus();
                } }
                forceIsEditingLink={ isEditingURL }
            />
            <div style={{
                padding: '0 16px 16px 16px'
            }}>
                <areoi.components.TextControl
                    label="Title"
                    value={ urlTitle }
                    onChange={ ( value ) => {
                        setAttributes( { 'url_title': value } )
                    } }
                />
            </div>
        </areoi.components.Popover>
    );

    return (
        <>
            <areoi.editor.BlockControls group="block">
                { ! isURLSet && (
                    <areoi.components.ToolbarButton
                        name="link"
                        icon={ areoi.icon.link }
                        title={ areoi.__( 'Link' ) }
                        shortcut={ areoi.keycodes.displayShortcut.primary( 'k' ) }
                        onClick={ startEditing }
                    />
                ) }
                { isURLSet && (
                    <areoi.components.ToolbarButton
                        name="link"
                        icon={ areoi.icon.linkOff }
                        title={ areoi.__( 'Unlink' ) }
                        shortcut={ areoi.keycodes.displayShortcut.primaryShift( 'k' ) }
                        onClick={ unlink }
                        isActive={ true }
                    />
                ) }
            </areoi.editor.BlockControls>
            { isSelected && (
                <areoi.components.KeyboardShortcuts
                    bindGlobal
                    shortcuts={ {
                        [ areoi.keycodes.rawShortcut.primary( 'k' ) ]: startEditing,
                        [ areoi.keycodes.rawShortcut.primaryShift( 'k' ) ]: () => {
                            unlink();
                            richTextRef.current?.focus();
                        },
                    } }
                />
            ) }
            { linkControl }
        </>
    );
}

export default URLPicker;