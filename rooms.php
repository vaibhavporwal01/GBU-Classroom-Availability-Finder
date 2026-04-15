<?php

/**
 * rooms.php — Room Registry and School Classifier
 * Single source of truth for all 85 GBU room ID-to-name mappings
 * and school/block prefix classification.
 */

/**
 * Returns all 85 room ID-to-name mappings.
 *
 * @return array<int, string>
 */
function getRooms(): array
{
    return [
        181 => 'VL201',
        182 => 'VL202',
        183 => 'VL203',
        184 => 'VL204',
        185 => 'VT201',
        186 => 'VT202',
        187 => 'VT203',
        188 => 'VT204',
        190 => 'BTLab1',
        191 => 'BT201',
        192 => 'BTLab2',
        195 => 'BTLab3',
        196 => 'BT204',
        198 => 'ET101',
        200 => 'IP101',
        202 => 'IP104',
        203 => 'IL202',
        204 => 'IP113',
        205 => 'IP106',
        206 => 'IL102',
        207 => 'IP103',
        208 => 'IL101',
        209 => 'IP102',
        210 => 'IL204',
        211 => 'IP108',
        212 => 'IP109',
        213 => 'IL203',
        214 => 'EL201',
        215 => 'IL205',
        216 => 'IL206',
        217 => 'IP105',
        218 => 'LL101',
        219 => 'LL102',
        220 => 'IP107',
        221 => 'IP110',
        223 => 'IL201',
        224 => 'IL200',
        225 => 'EL203',
        226 => 'EL104',
        227 => 'BT203',
        228 => 'EL101',
        230 => 'LH206',
        231 => 'LH205',
        232 => 'EP101',
        233 => 'EP105',
        234 => 'EL103',
        235 => 'VP201',
        236 => 'BP112',
        237 => 'EP203',
        238 => 'EP106',
        239 => 'EP202',
        240 => 'ET102',
        241 => 'ET103',
        245 => 'EL204',
        247 => 'EP201',
        248 => 'EP102',
        249 => 'V225',
        250 => 'EL102',
        255 => 'EL202',
        257 => 'IT201',
        258 => 'BL201',
        259 => 'BL202',
        260 => 'BL203',
        261 => 'BL204',
        262 => 'BL205',
        263 => 'BL206',
        264 => 'BL207',
        265 => 'VP102',
        266 => 'VP110',
        267 => 'BT202',
        268 => 'LL103',
        269 => 'LL104',
        270 => 'IT202',
        271 => 'IT203',
        272 => 'IT204',
        273 => 'EP103',
        274 => 'LH110',
        275 => 'LH103',
        276 => 'LH102',
        277 => 'LH104',
        278 => 'LH101',
        279 => 'LH108',
        280 => 'VP101',
        281 => 'VP103',
    ];
}

/**
 * Returns prefix-to-school mappings.
 * Order matters: longer/more-specific prefixes (BTLab) must be listed
 * before shorter ones (BT) within the same school group.
 *
 * @return array<string, string[]>  school => [prefix, ...]
 */
function getSchoolPrefixes(): array
{
    return [
        'SOICT'   => ['IL', 'IP', 'IT'],
        'SOE'     => ['EL', 'EP', 'ET'],
        'SOBT'    => ['BTLab', 'BL', 'BT'],   // BTLab before BT
        'SOVS/AS' => ['VL', 'VT', 'VP'],
        'Common'  => ['LL', 'LH', 'BP', 'V'],
    ];
}

/**
 * Returns the school/block category for a given room name.
 * BTLab prefix is checked before BT to avoid misclassification.
 * Falls back to 'Common' for any unrecognized prefix.
 *
 * @param string $roomName  e.g. 'IL202', 'BTLab1', 'V225'
 * @return string           One of: SOICT, SOE, SOBT, SOVS/AS, Common
 */
function getSchoolForRoom(string $roomName): string
{
    $prefixes = getSchoolPrefixes();

    foreach ($prefixes as $school => $schoolPrefixes) {
        foreach ($schoolPrefixes as $prefix) {
            if (strncmp($roomName, $prefix, strlen($prefix)) === 0) {
                return $school;
            }
        }
    }

    return 'Common';
}
