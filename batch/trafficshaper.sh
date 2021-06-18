#!/bin/sh


# script used to apply QOS/traffic shaping settings

logger "trafficshaper:: setting up traffic shaping config"

# hardcoded for now. enp3s0 is WAN enp4s0 is LAN
tc qdisc del dev enp3s0 root
tc qdisc del dev enp4s0 root

# shape outbound traffic
tc qdisc add dev enp3s0 root cake bandwidth 36Mbit diffserv4 triple-isolate nat nowash docsis

#logger "trafficshaper:: creating interface tshape for inbound traffic"
ip link del tshape
ip link add name tshape type ifb
tc qdisc del dev enp3s0 ingress
tc qdisc add dev enp3s0 handle ffff: ingress
tc qdisc del dev tshape root
logger "trafficshaper:: shape inbound at line rate"

# i'm usinc cable/docsis service.
tc qdisc add dev tshape root cake diffserv3 nat docsis

ifconfig tshape up
tc filter add dev enp3s0 parent ffff: protocol all prio 10 u32 match u32 0 0 flowid 1:1 action mirred egress redirect dev tshape