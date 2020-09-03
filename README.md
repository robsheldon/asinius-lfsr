# Asinius-LFSR

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

This is [Asinius library](https://github.com/robsheldon/asinius-core) module provides easy-to-use support for maximal-length linear feedback shift registers in PHP. It is tiny and fast and comes with default tap values that you can play with.

There's more information on LFSRs below this warning:

## CAUTION

**YOU SHOULD NOT USE THE DEFAULT TAPS IN PRODUCTION.** They are included for experimentation and ease-of-use when getting started. The code includes comments with links to other lists of taps. Play around with the default taps, and then find and use your own set of taps before deploying this in a production environment. Anyone that can guess your taps and get one of the numbers from your cycle can use these to quickly predict the rest of your cycle.

## What is a linear feedback shift register?

A linear feedback shift register allows you to pseudo-randomly cycle through a bit field. For example, creating a 4-bit LFSR with taps `[2. 3]` and a seed value of `1` in this library will produce the following sequence: `[6, 3, 7, 5, 4, 2, 1, 6, 3, 7, 5, 4...]`. Here, the pattern repeats every 7 cycles, but a 4-bit integer has 16 different combinations, so a lot of potential outputs are missing. This is where *maximal* (or "perfect") LFSRs become useful: certain tap values will cause the LFSR to visit every possible 4-bit value (except 0) exactly once. This library includes a selection of taps that produce maximal LFSRs for up to 256 bits. The sequence for a 4-bit LFSR using the default taps with a seed value of `1` is `[12, 6, 3, 13, 10, 5, 14, 7, 15, 11, 9, 8, 4, 2, 1, 12, 6, 3...]`.

This library uses a very simple Galois LFSR algorithm. There are other LFSR algorithms available. In this implementation of the Galois LFSR, "taps" are bits that get XOR'd during each cycle.

To learn more about LFSRs, see http://datagenetics.com/blog/november12017/index.html or https://en.wikipedia.org/wiki/Linear-feedback_shift_register .

## Status

I'm considering this complete and there are no planned future updates for this component.

## License

All of the Asinius project and its related modules are being released under the [MIT License](https://opensource.org/licenses/MIT). See LICENSE.
