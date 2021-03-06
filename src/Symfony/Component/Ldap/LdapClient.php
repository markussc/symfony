<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Ldap;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 * @author Francis Besset <francis.besset@gmail.com>
 * @author Charles Sarrazin <charles@sarraz.in>
 *
 * @deprecated The LdapClient class will be removed in Symfony 4.0. You should use the Ldap class instead.
 */
final class LdapClient implements LdapClientInterface
{
    private $ldap;

    public function __construct($host = null, $port = 389, $version = 3, $useSsl = false, $useStartTls = false, $optReferrals = false, LdapInterface $ldap = null)
    {
        $config = array(
            'host' => $host,
            'port' => $port,
            'version' => $version,
            'useSsl' => (bool) $useSsl,
            'useStartTls' => (bool) $useStartTls,
            'optReferrals' => (bool) $optReferrals,
        );

        $this->ldap = null !== $ldap ? $ldap : Ldap::create('ext_ldap', $config);
    }

    /**
     * {@inheritdoc}
     */
    public function bind($dn = null, $password = null)
    {
        $this->ldap->bind($dn, $password);
    }

    /**
     * {@inheritdoc}
     */
    public function find($dn, $query, $filter = '*')
    {
        @trigger_error('The "find" method is deprecated since version 3.1 and will be removed in 4.0. Use the "query" method instead.', E_USER_DEPRECATED);

        $query = $this->ldap->query($dn, $query, array('filter' => $filter));
        $entries = $query->execute();
        $result = array();

        foreach ($entries as $entry) {
            $resultEntry = array();

            foreach ($entry->getAttributes() as $attribute => $values) {
                $resultAttribute = $values;

                $resultAttribute['count'] = count($values);
                $resultEntry[] = $resultAttribute;
                $resultEntry[$attribute] = $resultAttribute;
            }

            $resultEntry['count'] = count($resultEntry) / 2;
            $result[] = $resultEntry;
        }

        $result['count'] = count($result);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function escape($subject, $ignore = '', $flags = 0)
    {
        return $this->ldap->escape($subject, $ignore, $flags);
    }
}
