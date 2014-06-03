GoProPHP
========

PHP tool for controlling a GoPro camera

goprophp.php can be run from the command line
goproinc.php can be included into other PHP projects (command line or web)

At present the settings are configured for the GoPro Hero3+ and might give unexpected results for other models. Please report such behaviours to the authors to improve future releases.

Usage
=====

Create a new object of the GoPro class:

$gopro = new GoPro('password'); //Use your GoPro's WiFi password

The above should work for almost all use cases. You may also use the more advanced constructor:

$gopro = new GoPro('password', 'Hero3+', 'ip address', 'port number');

You can then access functionality of the GoPro using:

$gopro->action('ACTION');

Supported Actions
=================

TURNON: Turns on the GoPro

TURNOFF: Turns off the GoPro

CMODE: Changes the mode. It is better to use the more specific 

SHOOT: Takes a photo (or starts video)

START: Starts recording a video (or takes a photo)

STOP: Stops video recording

PREVON: Turns preview mode on?

PREVOFF: Turns preview mode off?

VIDEO: Selects video mode

PHOTO: Selects photo mode

BURST: Selects burst mode

TLAPSE: Selects timelapse mode

UDOWN: Use if camera is upside-down

UUP: Use if camera is upside-up

4K#12: Records 4K video as 12fps

2.7K#24: Records 2.7K video at 24fps

960#48: Records 960px wide video at 48fps

Custom Commands
===============

If you know ana dditional custom command that can be executed on the GoPro via a URL of the form:

http://{ip_Address}:{port}/device/app?t={password}&p=command

This may be executed via:

$gopro->run(device, app, command);

Please notify the author of such commands (and the model of GoPro they apply to) for inclusion in a future release of this project.
