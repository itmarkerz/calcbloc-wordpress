import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import Edit from './edit';

// Rendered on the server (render.php), so the saved content is just the block comment with its attributes.
registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
