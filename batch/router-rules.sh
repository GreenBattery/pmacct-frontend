#!/bin/bash

# rules to setup a basic router based on NFTables if you so desire.

nft="/usr/sbin/nft"

#flush ruleset
${nft} flush ruleset

#create the tables called filter that we use for filtering traffic for each protocol family
${nft} add table ip filter
${nft} add table ip6 filter

#nat table for ipv4
${nft} add table nat

#Then add all the chains for the hooks that we want to use as well.
#for our router, we want input, forward, output and postrouting hooks.

${nft} add chain filter input { type filter hook input priority 0 \; }
${nft} add chain filter output {type filter hook output priority 0 \; }
${nft} add chain filter forward {type filter hook forward priority 0 \; }
${nft} add chain filter postrouting {type filter hook postrouting priority 0 \; }
${nft} add chain nat postrouting {type nat hook postrouting priority 100 \; }


${nft} add chain ip6 filter input { type filter hook input priority 0 \; }
${nft} add chain ip6 filter output {type filter hook output priority 0 \; }
${nft} add chain ip6 filter forward {type filter hook forward priority 0 \; }
${nft} add chain ip6 filter postrouting {type filter hook postrouting priority 0 \; }
${nft} add chain ip6 filter nat {type nat hook postrouting priority 100 \; }

#you should change these to point to the correct interfaces for your machine.
wan=enp3s0
lan=enp4s0

#FORWARDING RULESET

#forward traffic from WAN to LAN if related to established context
${nft} add rule filter forward iifname $wan oifname $lan ct state { established, related } accept

#forward from LAN to WAN always
${nft} add rule filter forward iifname $lan oifname $lan accept

#drop everything else from WAN to LAN
${nft} add rule filter forward iifname $wan oifname $lan counter drop

#ipv6 just in case we have this in future.
${nft} add rule ip6 filter forward iifname $wan oifname $lan ct state { established,related } accept
${nft} add rule ip6 filter forward iifname $wan oifname $lan icmpv6 type echo-request accept

#forward ipv6 from LAN to WAN.
${nft} add rule ip6 filter forward iifname $lan oifname $wan counter accept

#drop any other ipv6 from WAN to LAN
${nft} add rule filter forward iifname $wan oifname $lan counter drop





#INPUT CHAIN RULESET
#============================================================
${nft} add rule filter input ct state { established, related } accept

#accept loopback
${nft} add rule filter input iifname lo accept
# uncomment next rule to allow ssh in
#${nft} add rule filter input tcp dport ssh counter log accept

# count my data  received
${nft} add rule filter input iifname $lan ct state { established, related } counter
#accept  http, ssh, dns, smb, mysql from LAN
#${nft} add rule filter input iifname $lan tcp dport { 22, 53, 80, 443, 445, 3306 } accept

#accept all from lan.
${nft} add rule filter input iifname $lan accept

#accept dns and dhcp
${nft} add rule filter input iifname $lan udp dport { 53, 67, 68 } accept
${nft} add rule filter input iifname $lan ip protocol icmp accept
${nft} add rule filter input iifname $wan icmp type { echo-request } counter accept

${nft} add rule filter input counter log drop

${nft} add rule ip6 filter input ct state { established, related } accept
${nft} add rule ip6 filter input iifname lo accept


#uncomment next rule to allow ssh in over ipv6
#${nft} add rule ip6 filter input tcp dport ssh counter log accept

${nft} add rule ip6 filter input icmpv6 type { nd-neighbor-solicit, echo-request, nd-router-advert, nd-neighbor-advert } accept

${nft} add rule ip6 filter input counter drop




#OUTPUT CHAIN RULESET
#=======================================================
${nft} add rule filter output ct state { established, related, new } counter accept
${nft} add rule filter output iifname lo accept

${nft} add rule ip6 filter output ct state { established, related, new } accept
${nft} add rule ip6 filter output oifname lo accept


#SET MASQUERADING DIRECTIVE
${nft} add rule nat postrouting masquerade

logger -p 7 "OK, nftables rules added!"

