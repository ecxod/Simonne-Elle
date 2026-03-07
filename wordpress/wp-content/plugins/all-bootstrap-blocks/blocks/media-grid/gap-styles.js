/**
 * WordPress dependencies
 */
import { useStyleOverride } from '@wordpress/block-editor';

export default function GapStyles({ blockGap, clientId }) {
	// Build the CSS scoped to this block instance
	const css = `#block-${clientId} { --wp--style--unstable-gallery-gap: ${
		blockGap ?? 'var(--wp--style--block-gap, 0.5em)'
	} }`;

	// Insert/update a <style> in the editor canvas (handles iframe, etc.)
	useStyleOverride({
		id: `gap-styles-${clientId}`, // stable id so it updates instead of duplicating
		css,
	});

	// Nothing to render
	return null;
}
