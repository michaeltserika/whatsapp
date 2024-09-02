import { startStimulusApp } from '@symfony/stimulus-bridge';
import { registerReactControllerComponents } from '@symfony/ux-react';
const app = startStimulusApp();

const conspolite = require.context('./react/controllers', true, /\.(j|t)sx?$/)

registerReactControllerComponents(conspolite)